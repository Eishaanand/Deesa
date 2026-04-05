<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Exam;
use App\Models\Section;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\Throwable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class GeminiQuestionGeneratorService
{
    public function __construct(
        protected Client $client = new Client(),
    ) {
    }

    public function generateAndPersist(Section $section, string $difficulty, int $count): array
    {
        $questions = $this->requestQuestions($section, $difficulty, $count);

        $nextSequence = (int) $section->questions()->max('sequence');

        return collect($questions)->map(function (array $question) use ($section, $difficulty, &$nextSequence): array {
            $metadata = Arr::get($question, 'metadata', []);
            $metadata['origin_ref'] = Arr::get($metadata, 'origin_ref', (string) Str::uuid());
            $metadata['generated_via'] = 'gemini';

            $created = Question::create([
                'section_id' => $section->id,
                'type' => Arr::get($question, 'type', 'mcq'),
                'stem' => Arr::get($question, 'stem'),
                'passage' => Arr::get($question, 'passage'),
                'options' => Arr::get($question, 'options', []),
                'correct_answer' => Arr::get($question, 'correct_answer'),
                'explanation' => Arr::get($question, 'explanation'),
                'difficulty' => $difficulty,
                'topic' => Arr::get($question, 'topic', $section->name),
                'metadata' => $metadata,
                'sequence' => ++$nextSequence,
                'source' => 'gemini',
            ]);

            return $created->toArray();
        })->all();
    }

    public function populateFromLocalBank(Section $section, string $difficulty = 'medium'): array
    {
        return $this->cloneQuestionsFromPool($section, $difficulty, ['gemini', 'local_bank', 'manual', 'seed']);
    }

    public function populateFromGeminiBank(Section $section, string $difficulty = 'medium'): array
    {
        return $this->cloneQuestionsFromPool($section, $difficulty, ['gemini']);
    }

    public function populateMissingFromGeminiBank(Section $section, string $difficulty = 'medium', ?int $count = null): array
    {
        $count = $this->questionCountForSection($section);
        $existingCount = $section->questions()->count();
        $missing = max(($count ?? $this->questionCountForSection($section)) - $existingCount, 0);

        if ($missing === 0) {
            return [];
        }

        return $this->cloneQuestionsFromPool($section, $difficulty, ['gemini'], $missing);
    }

    public function regenerateExam(Exam $exam, string $difficulty = 'medium'): array
    {
        $exam->loadMissing('sections');

        if (! $this->hasApiKey()) {
            throw new RuntimeException('Gemini API key is missing.');
        }

        $summary = [];

        foreach ($exam->sections as $section) {
            $fromBank = $this->populateFromGeminiBank($section, $difficulty);
            $missing = max($this->questionCountForSection($section) - count($fromBank), 0);
            $fresh = $missing > 0 ? $this->generateAndPersist($section, $difficulty, $missing) : [];
            $generated = [...$fromBank, ...$fresh];

            $summary[] = [
                'section' => $section->name,
                'generated' => count($generated),
            ];
        }

        return $summary;
    }

    public function ensureQuestionBank(Section $section, int $count, string $difficulty = 'medium'): void
    {
        if ($this->hasApiKey()) {
            if ($section->questions()->where('source', 'gemini')->count() >= $count) {
                return;
            }
        } elseif ($section->questions()->count() >= $count) {
            return;
        }

        try {
            if (! $this->hasApiKey()) {
                throw new RuntimeException('Gemini API key is missing.');
            }

            $existingGemini = $section->questions()->where('source', 'gemini')->count();
            $missing = max($count - $existingGemini, 0);

            if ($missing === 0) {
                return;
            }

            $this->generateAndPersist($section, $difficulty, $missing);
        } catch (\Throwable $exception) {
            Log::warning('Gemini generation failed, using fallback question set.', [
                'section_id' => $section->id,
                'error' => $exception->getMessage(),
            ]);

            $missing = max($count - $section->questions()->count(), 0);

            if ($missing > 0) {
                $this->persistFallbackQuestions($section, $difficulty, $missing);
            }
        }
    }

    public function hasApiKey(): bool
    {
        return $this->resolveApiKey() !== '';
    }

    public function questionCountForSection(Section $section): int
    {
        return match ($section->type) {
            'verbal_reasoning' => 44,
            'decision_making' => 35,
            'quantitative_reasoning' => 36,
            'situational_judgement' => 69,
            default => max((int) ($section->exam?->questions_per_section ?? 1), 1),
        };
    }

    protected function requestQuestions(Section $section, string $difficulty, int $count): array
    {
        $apiKey = $this->resolveApiKey();
        $model = $this->resolveModel();
        $baseUrl = config('services.gemini.base_url');

        if (! $apiKey) {
            throw new RuntimeException('Gemini API key is missing.');
        }

        $prompt = <<<PROMPT
Generate {$count} UCAT {$section->name} questions in strict JSON.

Return an array. Each item must contain:
- type
- topic
- stem
- passage (nullable)
- options (array of 4 options)
- correct_answer
- explanation
- metadata (object)

Difficulty: {$difficulty}
Section type: {$section->type}

Do not return markdown. Do not wrap in code fences.
PROMPT;

        try {
            $response = $this->client->post("{$baseUrl}/{$model}:generateContent", [
                'query' => ['key' => $apiKey],
                'json' => [
                    'contents' => [[
                        'parts' => [[
                            'text' => $prompt,
                        ]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'response_mime_type' => 'application/json',
                    ],
                ],
            ]);
        } catch (ClientException $exception) {
            $status = $exception->getResponse()?->getStatusCode();
            $body = json_decode((string) $exception->getResponse()?->getBody(), true);
            $message = data_get($body, 'error.message', $exception->getMessage());

            Log::warning('Gemini API request failed', [
                'status' => $status,
                'message' => $message,
                'model' => $model,
            ]);

            if ($status === 429) {
                throw new RuntimeException('Gemini quota exceeded. Check Google AI Studio billing and quota limits for this API key.');
            }

            if ($status === 404) {
                throw new RuntimeException("Gemini model '{$model}' was not found for this API version.");
            }

            throw new RuntimeException("Gemini request failed: {$message}");
        }

        $payload = json_decode((string) $response->getBody(), true);
        $text = data_get($payload, 'candidates.0.content.parts.0.text');
        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            Log::warning('Invalid Gemini question payload', ['payload' => $payload]);
            throw new RuntimeException('Gemini returned an invalid question payload.');
        }

        return $decoded;
    }

    protected function persistFallbackQuestions(Section $section, string $difficulty, int $count): void
    {
        $nextSequence = (int) $section->questions()->max('sequence');

        for ($index = 1; $index <= $count; $index++) {
            $number = $nextSequence + $index;
            $passage = str_contains($section->type, 'verbal')
                ? "Passage {$number}: UCAT-style reading passage for {$section->name}. Review the evidence carefully and identify the strongest answer based on the text."
                : null;

            Question::create([
                'section_id' => $section->id,
                'type' => 'mcq',
                'stem' => "{$section->name} generated question {$number}: choose the best answer using timed exam logic.",
                'passage' => $passage,
                'options' => [
                    'Option A',
                    'Option B',
                    'Option C',
                    'Option D',
                ],
                'correct_answer' => 'Option B',
                'explanation' => 'Fallback explanation: Option B best matches the intended UCAT-style reasoning.',
                'difficulty' => $difficulty,
                'topic' => $section->name,
                'metadata' => [
                    'fallback' => true,
                    'origin_ref' => (string) Str::uuid(),
                    'generated_via' => 'fallback',
                ],
                'sequence' => $number,
                'source' => 'fallback',
            ]);
        }
    }

    protected function cloneQuestionsFromPool(Section $section, string $difficulty, array $sources, ?int $count = null): array
    {
        $count ??= $this->questionCountForSection($section);
        $nextSequence = (int) $section->questions()->max('sequence');
        $usedOrigins = $this->usedOriginReferencesForOtherExams($section);

        $pool = Question::query()
            ->where('section_id', '!=', $section->id)
            ->whereIn('source', $sources)
            ->whereHas('section', fn ($query) => $query->where('type', $section->type))
            ->where('difficulty', $difficulty)
            ->get()
            ->reject(fn (Question $question) => in_array($this->originReferenceForQuestion($question), $usedOrigins, true))
            ->unique(fn (Question $question) => $this->originReferenceForQuestion($question))
            ->shuffle()
            ->take($count)
            ->values();

        if ($pool->count() < $count) {
            $pool = Question::query()
                ->where('section_id', '!=', $section->id)
                ->whereIn('source', $sources)
                ->whereHas('section', fn ($query) => $query->where('type', $section->type))
                ->get()
                ->reject(fn (Question $question) => in_array($this->originReferenceForQuestion($question), $usedOrigins, true))
                ->unique(fn (Question $question) => $this->originReferenceForQuestion($question))
                ->shuffle()
                ->take($count)
                ->values();
        }

        return $pool->map(function (Question $question) use ($section, &$nextSequence): array {
            $metadata = array_merge($question->metadata ?? [], [
                'cloned_from_question_id' => $question->id,
                'origin_ref' => $this->originReferenceForQuestion($question),
            ]);

            $created = Question::create([
                'section_id' => $section->id,
                'type' => $question->type,
                'stem' => $question->stem,
                'passage' => $question->passage,
                'options' => $question->options,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
                'difficulty' => $question->difficulty,
                'topic' => $question->topic,
                'metadata' => $metadata,
                'sequence' => ++$nextSequence,
                'source' => in_array($question->source, ['gemini', 'local_bank'], true) ? 'local_bank' : $question->source,
            ]);

            return $created->toArray();
        })->all();
    }

    protected function usedOriginReferencesForOtherExams(Section $section): array
    {
        $examId = $section->exam_id;

        return Question::query()
            ->whereHas('section', function ($query) use ($section, $examId): void {
                $query->where('type', $section->type)
                    ->where('exam_id', '!=', $examId);
            })
            ->get()
            ->map(fn (Question $question) => $this->originReferenceForQuestion($question))
            ->unique()
            ->values()
            ->all();
    }

    protected function originReferenceForQuestion(Question $question): string
    {
        return (string) data_get($question->metadata, 'origin_ref', 'legacy-'.$question->id);
    }

    protected function resolveApiKey(): string
    {
        $configured = trim((string) config('services.gemini.api_key', ''));

        if ($configured !== '') {
            return $configured;
        }

        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            return '';
        }

        foreach (preg_split("/\r\n|\n|\r/", (string) File::get($envPath)) as $line) {
            if (! str_starts_with($line, 'GEMINI_API_KEY=')) {
                continue;
            }

            return trim(substr($line, strlen('GEMINI_API_KEY=')), "\"' \t");
        }

        return '';
    }

    protected function resolveModel(): string
    {
        $configured = trim((string) config('services.gemini.model', ''));

        if ($configured !== '' && $configured !== 'gemini-1.5-flash') {
            return $configured;
        }

        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            return $configured !== '' ? $configured : 'gemini-2.5-flash';
        }

        foreach (preg_split("/\r\n|\n|\r/", (string) File::get($envPath)) as $line) {
            if (! str_starts_with($line, 'GEMINI_MODEL=')) {
                continue;
            }

            $resolved = trim(substr($line, strlen('GEMINI_MODEL=')), "\"' \t");

            return $resolved !== '' ? $resolved : 'gemini-2.5-flash';
        }

        return $configured !== '' ? $configured : 'gemini-2.5-flash';
    }
}
