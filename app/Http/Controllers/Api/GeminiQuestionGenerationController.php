<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Services\GeminiQuestionGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeminiQuestionGenerationController extends Controller
{
    public function __invoke(Request $request, GeminiQuestionGeneratorService $generator): JsonResponse
    {
        $validated = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'count' => ['required', 'integer', 'min:1', 'max:25'],
        ]);

        $section = Section::findOrFail($validated['section_id']);
        $questions = $generator->generateAndPersist($section, $validated['difficulty'], $validated['count']);

        return response()->json([
            'generated' => count($questions),
            'questions' => $questions,
        ]);
    }
}
