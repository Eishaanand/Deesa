<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserExam;
use App\Services\ExamEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttemptAnswerController extends Controller
{
    public function __invoke(Request $request, UserExam $attempt, ExamEngineService $engine): JsonResponse
    {
        abort_unless($attempt->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'selected_answer' => ['nullable', 'string'],
            'time_spent_seconds' => ['required', 'integer', 'min:0'],
        ]);

        $answer = $engine->saveAnswer($attempt, $validated);

        return response()->json([
            'saved' => true,
            'answer_id' => $answer->id,
        ]);
    }
}
