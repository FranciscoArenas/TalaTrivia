<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTriviaRequest;
use App\Models\Trivia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TriviaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/trivias",
     *     summary="Listar trivias",
     *     tags={"Trivias"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado",
     *         required=false,
     *         @OA\Schema(type="string", enum={"draft", "active", "completed"})
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
     *         description="Lista de trivias",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="trivias",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Trivia")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Trivia::with(['creator:id,name', 'questions', 'users'])
                      ->select(['id', 'name', 'description', 'status', 'created_by', 'starts_at', 'ends_at', 'created_at']);

        // Filtrar por estado si se especifica
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filtrar por creador si se especifica
        if ($request->has('created_by')) {
            $query->byCreator($request->created_by);
        }

        $trivias = $query->orderBy('created_at', 'desc')->get();

        // Agregar estadísticas básicas
        $trivias->each(function ($trivia) {
            $trivia->questions_count = $trivia->questions->count();
            $trivia->users_count = $trivia->users->count();
            $trivia->completed_count = $trivia->users()->wherePivot('completed', true)->count();
            unset($trivia->questions, $trivia->users);
        });

        return response()->json([
            'trivias' => $trivias
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/trivias",
     *     summary="Crear nueva trivia",
     *     tags={"Trivias"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","question_ids","user_ids"},
     *             @OA\Property(property="name", type="string", example="Trivia de Historia"),
     *             @OA\Property(property="description", type="string", example="Una trivia sobre historia mundial"),
     *             @OA\Property(property="starts_at", type="string", format="date-time", example="2023-01-01T10:00:00Z"),
     *             @OA\Property(property="ends_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
     *             @OA\Property(
     *                 property="question_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             ),
     *             @OA\Property(
     *                 property="user_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trivia creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trivia creada exitosamente"),
     *             @OA\Property(property="trivia", ref="#/components/schemas/Trivia")
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
    public function store(StoreTriviaRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $trivia = Trivia::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status ?? 'draft',
                'created_by' => $request->user()->id,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
            ]);

            // Asignar preguntas con orden
            foreach ($request->question_ids as $index => $questionId) {
                $trivia->questions()->attach($questionId, ['order' => $index + 1]);
            }

            // Asignar usuarios
            $trivia->users()->attach($request->user_ids);

            DB::commit();

            $trivia->load(['creator:id,name', 'questions:id,question,difficulty', 'users:id,name,email']);

            return response()->json([
                'message' => 'Trivia creada exitosamente',
                'trivia' => $trivia
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la trivia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $trivia = Trivia::with([
                'creator:id,name',
                'questions' => function($query) {
                    $query->with('options')->orderBy('trivia_questions.order');
                },
                'users' => function($query) {
                    $query->select('users.id', 'users.name', 'users.email')
                          ->withPivot('total_score', 'completed', 'started_at', 'completed_at');
                }
            ])
            ->findOrFail($id);

        return response()->json([
            'trivia' => $trivia
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $trivia = Trivia::findOrFail($id);

        // Verificar permisos
        if (!$request->user()->isAdmin() || $trivia->created_by !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permisos para editar esta trivia'
            ], 403);
        }

        // No permitir edición si la trivia está activa y tiene participaciones
        if ($trivia->status === 'active' && $trivia->users()->wherePivot('started_at', '!=', null)->exists()) {
            return response()->json([
                'message' => 'No se puede editar una trivia activa con participaciones iniciadas'
            ], 400);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:draft,active,completed',
            'starts_at' => 'sometimes|nullable|date',
            'ends_at' => 'sometimes|nullable|date|after:starts_at',
            'question_ids' => 'sometimes|array|min:1',
            'question_ids.*' => 'exists:questions,id',
            'user_ids' => 'sometimes|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar campos básicos
            $trivia->update([
                'name' => $validatedData['name'] ?? $trivia->name,
                'description' => $validatedData['description'] ?? $trivia->description,
                'status' => $validatedData['status'] ?? $trivia->status,
                'starts_at' => $validatedData['starts_at'] ?? $trivia->starts_at,
                'ends_at' => $validatedData['ends_at'] ?? $trivia->ends_at,
            ]);

            // Actualizar preguntas si se especifican
            if (isset($validatedData['question_ids'])) {
                $trivia->questions()->detach();
                foreach ($validatedData['question_ids'] as $index => $questionId) {
                    $trivia->questions()->attach($questionId, ['order' => $index + 1]);
                }
            }

            // Actualizar usuarios si se especifican
            if (isset($validatedData['user_ids'])) {
                $trivia->users()->detach();
                $trivia->users()->attach($validatedData['user_ids']);
            }

            DB::commit();

            $trivia->load(['creator:id,name', 'questions:id,question,difficulty', 'users:id,name,email']);

            return response()->json([
                'message' => 'Trivia actualizada exitosamente',
                'trivia' => $trivia
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la trivia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $trivia = Trivia::findOrFail($id);

        // Verificar permisos
        if (!request()->user()->isAdmin() || $trivia->created_by !== request()->user()->id) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar esta trivia'
            ], 403);
        }

        // No permitir eliminación si hay participaciones
        if ($trivia->users()->wherePivot('started_at', '!=', null)->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una trivia con participaciones iniciadas'
            ], 400);
        }

        $trivia->delete();

        return response()->json([
            'message' => 'Trivia eliminada exitosamente'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/trivias/{id}/ranking",
     *     summary="Obtener ranking de una trivia específica",
     *     tags={"Trivias"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la trivia",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ranking de la trivia",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="trivia",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string")
     *             ),
     *             @OA\Property(
     *                 property="ranking",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TriviaRanking")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trivia no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function ranking(string $id): JsonResponse
    {
        $trivia = Trivia::findOrFail($id);

        $ranking = $trivia->getUserRanking();

        return response()->json([
            'trivia' => [
                'id' => $trivia->id,
                'name' => $trivia->name,
                'description' => $trivia->description,
            ],
            'ranking' => $ranking->map(function ($user, $index) {
                return [
                    'position' => $index + 1,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'score' => $user->total_score,
                    'completed_at' => $user->completed_at,
                ];
            })
        ]);
    }
}
