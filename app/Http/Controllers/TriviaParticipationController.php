<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitAnswerRequest;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Trivia;
use App\Models\UserAnswer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TriviaParticipationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/my-trivias",
     *     summary="Obtener trivias asignadas al jugador",
     *     tags={"Participación en Trivias"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de trivias asignadas",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="trivias",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="questions_count", type="integer"),
     *                     @OA\Property(
     *                         property="participation",
     *                         type="object",
     *                         @OA\Property(property="total_score", type="integer", nullable=true),
     *                         @OA\Property(property="completed", type="boolean"),
     *                         @OA\Property(property="started_at", type="string", format="date-time", nullable=true),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Solo jugadores pueden acceder",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function myTrivias(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isPlayer()) {
            return response()->json([
                'message' => 'Solo los jugadores pueden acceder a sus trivias asignadas'
            ], 403);
        }

        $trivias = $user->assignedTrivias()
                        ->select(['trivias.id', 'trivias.name', 'trivias.description', 'trivias.status', 'trivias.starts_at', 'trivias.ends_at'])
                        ->withPivot('total_score', 'completed', 'started_at', 'completed_at')
                        ->withCount('questions')
                        ->get();

        return response()->json([
            'trivias' => $trivias->map(function ($trivia) {
                return [
                    'id' => $trivia->id,
                    'name' => $trivia->name,
                    'description' => $trivia->description,
                    'status' => $trivia->status,
                    'starts_at' => $trivia->starts_at,
                    'ends_at' => $trivia->ends_at,
                    'questions_count' => $trivia->questions_count,
                    'participation' => [
                        'total_score' => $trivia->pivot->total_score,
                        'completed' => $trivia->pivot->completed,
                        'started_at' => $trivia->pivot->started_at,
                        'completed_at' => $trivia->pivot->completed_at,
                    ]
                ];
            })
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/trivias/{triviaId}/start",
     *     summary="Iniciar participación en una trivia",
     *     tags={"Participación en Trivias"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="triviaId",
     *         in="path",
     *         description="ID de la trivia",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trivia iniciada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trivia iniciada exitosamente"),
     *             @OA\Property(
     *                 property="trivia",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ya has iniciado esta trivia",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No puedes participar en esta trivia",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function startTrivia(Request $request, string $triviaId): JsonResponse
    {
        $user = $request->user();
        $trivia = Trivia::findOrFail($triviaId);

        if (!$trivia->canUserParticipate($user->id)) {
            return response()->json([
                'message' => 'No puedes participar en esta trivia'
            ], 403);
        }

        $participation = $trivia->getUserParticipation($user->id);

        // Si ya empezó, devolver el estado actual
        if ($participation && $participation->pivot->started_at) {
            return response()->json([
                'message' => 'Ya has iniciado esta trivia',
                'participation' => $participation->pivot
            ], 400);
        }

        // Marcar como iniciada
        $trivia->users()->updateExistingPivot($user->id, [
            'started_at' => Carbon::now()
        ]);

        return response()->json([
            'message' => 'Trivia iniciada exitosamente',
            'trivia' => [
                'id' => $trivia->id,
                'name' => $trivia->name,
                'description' => $trivia->description,
            ]
        ]);
    }

    /**
     * Get trivia questions for player (without revealing correct answers)
     */
    public function getTriviaQuestions(Request $request, string $triviaId): JsonResponse
    {
        $user = $request->user();
        $trivia = Trivia::findOrFail($triviaId);

        if (!$trivia->canUserParticipate($user->id)) {
            return response()->json([
                'message' => 'No puedes acceder a las preguntas de esta trivia'
            ], 403);
        }

        $participation = $trivia->getUserParticipation($user->id);
        if (!$participation || !$participation->pivot->started_at) {
            return response()->json([
                'message' => 'Debes iniciar la trivia primero'
            ], 400);
        }

        // Obtener preguntas sin revelar respuestas correctas
        $questions = $trivia->getQuestionsForPlayer();

        // Obtener respuestas ya enviadas por el usuario
        $userAnswers = UserAnswer::where('trivia_id', $triviaId)
                                ->where('user_id', $user->id)
                                ->get()
                                ->keyBy('question_id');

        $questionsWithAnswers = $questions->map(function ($question) use ($userAnswers) {
            $userAnswer = $userAnswers->get($question->id);

            return [
                'id' => $question->id,
                'question' => $question->question,
                'options' => $question->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'option_text' => $option->option_text,
                    ];
                }),
                'user_answer' => $userAnswer ? [
                    'selected_option_id' => $userAnswer->question_option_id,
                    'answered_at' => $userAnswer->created_at,
                ] : null,
                'answered' => (bool) $userAnswer,
            ];
        });

        return response()->json([
            'trivia' => [
                'id' => $trivia->id,
                'name' => $trivia->name,
                'description' => $trivia->description,
            ],
            'questions' => $questionsWithAnswers,
            'total_questions' => $questions->count(),
            'answered_questions' => $userAnswers->count(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/trivias/{triviaId}/answer",
     *     summary="Enviar respuesta a una pregunta de la trivia",
     *     tags={"Participación en Trivias"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="triviaId",
     *         in="path",
     *         description="ID de la trivia",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question_id","question_option_id"},
     *             @OA\Property(property="question_id", type="integer", example=1),
     *             @OA\Property(property="question_option_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta enviada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Respuesta enviada correctamente"),
     *             @OA\Property(property="is_correct", type="boolean", example=true),
     *             @OA\Property(property="points_earned", type="integer", example=2),
     *             @OA\Property(property="total_score", type="integer", example=8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function submitAnswer(SubmitAnswerRequest $request, string $triviaId): JsonResponse
    {
        $user = $request->user();
        $trivia = Trivia::findOrFail($triviaId);
        $question = Question::findOrFail($request->question_id);
        $selectedOption = QuestionOption::findOrFail($request->question_option_id);

        try {
            DB::beginTransaction();

            // Verificar respuesta correcta
            $correctOption = $question->correctOption;
            $isCorrect = $selectedOption->is_correct;
            $pointsEarned = $isCorrect ? $question->points : 0;

            // Guardar respuesta
            UserAnswer::create([
                'trivia_id' => $triviaId,
                'user_id' => $user->id,
                'question_id' => $question->id,
                'question_option_id' => $selectedOption->id,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);

            // Actualizar puntaje total del usuario en la trivia
            $totalScore = UserAnswer::where('trivia_id', $triviaId)
                                  ->where('user_id', $user->id)
                                  ->sum('points_earned');

            $trivia->users()->updateExistingPivot($user->id, [
                'total_score' => $totalScore
            ]);

            // Verificar si completó todas las preguntas
            $totalQuestions = $trivia->questions()->count();
            $answeredQuestions = UserAnswer::where('trivia_id', $triviaId)
                                         ->where('user_id', $user->id)
                                         ->count();

            $completed = $answeredQuestions >= $totalQuestions;

            if ($completed) {
                $trivia->users()->updateExistingPivot($user->id, [
                    'completed' => true,
                    'completed_at' => Carbon::now()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Respuesta enviada exitosamente',
                'result' => [
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'total_score' => $totalScore,
                    'completed' => $completed,
                    'answered_questions' => $answeredQuestions,
                    'total_questions' => $totalQuestions,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al enviar respuesta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's results for a specific trivia
     */
    public function getResults(Request $request, string $triviaId): JsonResponse
    {
        $user = $request->user();
        $trivia = Trivia::findOrFail($triviaId);

        $participation = $trivia->getUserParticipation($user->id);
        if (!$participation) {
            return response()->json([
                'message' => 'No tienes participación en esta trivia'
            ], 403);
        }

        // Obtener respuestas del usuario con detalles
        $userAnswers = UserAnswer::where('trivia_id', $triviaId)
                                ->where('user_id', $user->id)
                                ->with(['question:id,question,difficulty', 'selectedOption:id,option_text,is_correct'])
                                ->get();

        $correctAnswers = $userAnswers->where('is_correct', true)->count();
        $totalQuestions = $trivia->questions()->count();
        $totalPossibleScore = $trivia->getTotalPossibleScore();

        return response()->json([
            'trivia' => [
                'id' => $trivia->id,
                'name' => $trivia->name,
                'description' => $trivia->description,
            ],
            'participation' => [
                'total_score' => $participation->pivot->total_score,
                'completed' => $participation->pivot->completed,
                'started_at' => $participation->pivot->started_at,
                'completed_at' => $participation->pivot->completed_at,
            ],
            'statistics' => [
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'total_score' => $participation->pivot->total_score,
                'total_possible_score' => $totalPossibleScore,
                'percentage' => $totalPossibleScore > 0 ? round(($participation->pivot->total_score / $totalPossibleScore) * 100, 2) : 0,
            ],
            'answers' => $userAnswers->map(function ($answer) {
                return [
                    'question' => $answer->question->question,
                    'difficulty' => $answer->question->difficulty,
                    'selected_option' => $answer->selectedOption->option_text,
                    'is_correct' => $answer->is_correct,
                    'points_earned' => $answer->points_earned,
                    'answered_at' => $answer->created_at,
                ];
            })
        ]);
    }
}
