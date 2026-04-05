<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Section;
use App\Services\GeminiQuestionGeneratorService;
use Livewire\Component;

class AdminQuestionGenerator extends Component
{
    public Exam $exam;
    public ?int $sectionId = null;
    public string $difficulty = 'medium';
    public ?string $status = null;
    public bool $apiConfigured = false;

    public function mount(Exam $exam): void
    {
        $this->exam = $exam;
        $this->sectionId = $exam->sections->first()?->id;
        $this->apiConfigured = app(GeminiQuestionGeneratorService::class)->hasApiKey();
    }

    public function generate(GeminiQuestionGeneratorService $service): void
    {
        if (! $service->hasApiKey()) {
            $this->status = 'Gemini API key is missing in .env. Set GEMINI_API_KEY first.';

            return;
        }

        $this->validate([
            'sectionId' => ['required', 'integer'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
        ]);

        $section = Section::findOrFail($this->sectionId);
        $count = $service->questionCountForSection($section);
        $created = $service->generateAndPersist($section, $this->difficulty, $count);
        $this->status = count($created).' AI questions generated for '.$section->name.'.';
        $this->exam->refresh();
    }

    public function render()
    {
        return view('livewire.admin-question-generator');
    }
}
