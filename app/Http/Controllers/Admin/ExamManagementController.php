<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use App\Services\GeminiQuestionGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ExamManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.exams.index', [
            'exams' => Exam::withCount('sections')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.exams.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_ai_supported' => ['nullable', 'boolean'],
        ]);

        $exam = DB::transaction(function () use ($validated): Exam {
            $sections = [
                ['name' => 'Verbal Reasoning', 'type' => 'verbal_reasoning', 'time_limit_seconds' => 1260, 'sequence' => 1],
                ['name' => 'Decision Making', 'type' => 'decision_making', 'time_limit_seconds' => 1860, 'sequence' => 2],
                ['name' => 'Quantitative Reasoning', 'type' => 'quantitative_reasoning', 'time_limit_seconds' => 1500, 'sequence' => 3],
                ['name' => 'Situational Judgement', 'type' => 'situational_judgement', 'time_limit_seconds' => 1560, 'sequence' => 4],
            ];

            $totalDuration = collect($sections)->sum('time_limit_seconds');

            $exam = Exam::create([
                ...$validated,
                'slug' => Str::slug($validated['title']).'-'.Str::lower(Str::random(6)),
                'sequence_number' => ((int) Exam::max('sequence_number')) + 1,
                'total_duration_seconds' => $totalDuration,
                'requires_subscription' => false,
                'questions_per_section' => 0,
                'is_ai_supported' => (bool) ($validated['is_ai_supported'] ?? false),
            ]);

            foreach ($sections as $section) {
                $exam->sections()->create([
                    ...$section,
                    'instructions' => 'Answer all questions in order. The section auto-submits when the timer ends.',
                ]);
            }

            return $exam;
        });

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('status', 'New UCAT exam shell created. Use the Gemini generator to populate full sections.');
    }

    public function show(Exam $exam): View
    {
        $exam->load('sections.questions');

        return view('admin.exams.show', compact('exam'));
    }

    public function regenerate(Exam $exam, GeminiQuestionGeneratorService $generator): RedirectResponse
    {
        if ($exam->is_ai_supported && ! $generator->hasApiKey()) {
            return redirect()
                ->route('admin.exams.show', $exam)
                ->with('status', 'Gemini API key is missing. Add GEMINI_API_KEY in .env first.');
        }

        $summary = $exam->is_ai_supported
            ? $generator->regenerateExam($exam, 'medium')
            : collect($exam->loadMissing('sections')->sections)->map(function ($section) use ($generator): array {
                $generated = $generator->populateFromLocalBank($section, 'medium');

                return [
                    'section' => $section->name,
                    'generated' => count($generated),
                ];
            })->all();

        $message = collect($summary)
            ->map(fn (array $item) => "{$item['section']}: {$item['generated']}")
            ->implode(', ');

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('status', ($exam->is_ai_supported ? 'Full mock regenerated from Gemini.' : 'Full mock generated from local cached questions.').' '.$message);
    }

    public function toggleSource(Exam $exam): RedirectResponse
    {
        $exam->update([
            'is_ai_supported' => ! $exam->is_ai_supported,
        ]);

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('status', 'Source mode changed to '.($exam->fresh()->is_ai_supported ? 'Gemini API' : 'local cached question bank').'.');
    }

    public function edit(Exam $exam): View
    {
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_ai_supported' => ['nullable', 'boolean'],
        ]);

        $exam->update([
            ...$validated,
            'slug' => Str::slug($validated['title']),
        ]);

        return redirect()->route('admin.exams.show', $exam);
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $title = $exam->title;
        $exam->delete();

        return redirect()
            ->route('admin.exams.index')
            ->with('status', "Exam '{$title}' deleted. All user attempts and answer data for this exam were removed as well.");
    }
}
