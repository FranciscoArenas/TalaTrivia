<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/questions",
     *     summary="Listar preguntas",
     *     tags={"Preguntas"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="difficulty",
     *         in="query",
     *         description="Filtrar por dificultad",
     *         required=false,
     *         @OA\Schema(type="string", enum={"easy", "medium", "hard"})
     *     ),
     *     @OA\Parameter(
     *         name="created_by",
     *         in="query",
     *         description="Filtrar por creador",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de preguntas",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="questions",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Question")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Question::with(['options', 'creator:id,name'])
                        ->select(['id', 'question', 'difficulty', 'created_by', 'created_at']);

        // Filtrar por dificultad si se especifica
        if ($request->has('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        // Filtrar por creador si se especifica
        if ($request->has('created_by')) {
            $query->byCreator($request->created_by);
        }

        $questions = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'questions' => $questions
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/questions",
     *     summary="Crear nueva pregunta",
     *     tags={"Preguntas"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question","difficulty","options"},
     *             @OA\Property(property="question", type="string", example="¿Cuál es la capital de Francia?"),
     *             @OA\Property(property="difficulty", type="string", enum={"easy", "medium", "hard"}, example="easy"),
     *             @OA\Property(
     *                 property="options",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"option_text","is_correct"},
     *                     @OA\Property(property="option_text", type="string", example="París"),
     *                     @OA\Property(property="is_correct", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pregunta creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pregunta creada exitosamente"),
     *             @OA\Property(property="question", ref="#/components/schemas/Question")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreQuestionRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $question = Question::create([
                'question' => $request->question,
                'difficulty' => $request->difficulty,
                'created_by' => $request->user()->id,
            ]);

            // Crear las opciones
            foreach ($request->options as $optionData) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['option_text'],
                    'is_correct' => $optionData['is_correct'],
                ]);
            }

            DB::commit();

            $question->load(['options', 'creator:id,name']);

            return response()->json([
                'message' => 'Pregunta creada exitosamente',
                'question' => $question
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la pregunta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $question = Question::with(['options', 'creator:id,name'])
                           ->findOrFail($id);

        return response()->json([
            'question' => $question
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $question = Question::findOrFail($id);

        // Verificar que el usuario puede editar esta pregunta
        if (!$request->user()->isAdmin() || $question->created_by !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permisos para editar esta pregunta'
            ], 403);
        }

        $validatedData = $request->validate([
            'question' => 'sometimes|string|min:10',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'options' => 'sometimes|array|min:2|max:6',
            'options.*.id' => 'sometimes|exists:question_options,id',
            'options.*.option_text' => 'required_with:options|string|max:255',
            'options.*.is_correct' => 'required_with:options|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar la pregunta
            $question->update([
                'question' => $validatedData['question'] ?? $question->question,
                'difficulty' => $validatedData['difficulty'] ?? $question->difficulty,
            ]);

            // Si se envían opciones, actualizarlas
            if (isset($validatedData['options'])) {
                // Validar que hay exactamente una respuesta correcta
                $correctAnswers = collect($validatedData['options'])->where('is_correct', true)->count();
                if ($correctAnswers !== 1) {
                    return response()->json([
                        'message' => 'Debe haber exactamente una respuesta correcta'
                    ], 400);
                }

                // Eliminar opciones existentes
                $question->options()->delete();

                // Crear nuevas opciones
                foreach ($validatedData['options'] as $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                    ]);
                }
            }

            DB::commit();

            $question->load(['options', 'creator:id,name']);

            return response()->json([
                'message' => 'Pregunta actualizada exitosamente',
                'question' => $question
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la pregunta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $question = Question::findOrFail($id);

        // Verificar que el usuario puede eliminar esta pregunta
        if (!request()->user()->isAdmin() || $question->created_by !== request()->user()->id) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar esta pregunta'
            ], 403);
        }

        // Verificar que la pregunta no esté siendo usada en trivias activas
        $activeTrivias = $question->trivias()->where('status', 'active')->count();
        if ($activeTrivias > 0) {
            return response()->json([
                'message' => 'No se puede eliminar una pregunta que está siendo usada en trivias activas'
            ], 400);
        }

        $question->delete();

        return response()->json([
            'message' => 'Pregunta eliminada exitosamente'
        ]);
    }
}
