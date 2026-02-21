<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'source_code' => 'required',
            'language_id' => 'required',
        ]);

        $mockToken = md5(uniqid((string) rand(), true));

        return response()->json([
            'token' => $mockToken,
        ]);
    }

    public function show(string $token): JsonResponse
    {
        return response()->json([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => "Hello World\n",
            'stderr' => null,
            'compile_output' => null,
            'message' => null,
            'time' => '0.01',
            'memory' => '2048',
        ]);
    }

    public function storeExerciseSubmission(Request $request, string $id): JsonResponse
    {
        $exercise = Exercise::findOrFail($id);
        $user = $request->user();

        $submission = Submission::create([
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'code' => $request->input('code'),
            'status' => 'passed',
        ]);

        return response()->json($submission, 201);
    }
}
