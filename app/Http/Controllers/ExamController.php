<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\UserExam;
use App\Services\ExamAccessService;
use App\Services\ExamEngineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request, ExamAccessService $access): View
    {
        $exams = Exam::with('sections.questions')->where('status', 'published')->orderBy('sequence_number')->get();

        return view('exams.index', [
            'examCards' => $access->mapForUser($request->user(), $exams),
        ]);
    }

    public function show(Request $request, Exam $exam, ExamAccessService $access): View
    {
        $exam->load('sections.questions');

        return view('exams.show', [
            'exam' => $exam,
            'canStart' => $access->canStart($request->user(), $exam),
            'subscriptionPrice' => 30,
        ]);
    }

    public function start(Request $request, Exam $exam, ExamEngineService $engine, ExamAccessService $access): RedirectResponse
    {
        abort_unless($access->canStart($request->user(), $exam), 403);

        $attempt = $engine->startAttempt($request->user(), $exam);

        return redirect()->route('exams.take', $attempt);
    }

    public function take(UserExam $attempt): View
    {
        $attempt->load('exam.sections.questions', 'answers');

        abort_unless($attempt->user_id === auth()->id(), 403);

        return view('exams.take', compact('attempt'));
    }

    public function submit(UserExam $attempt, ExamEngineService $engine): RedirectResponse
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        $engine->submitAttempt($attempt);

        return redirect()->route('results.show', $attempt);
    }
}
