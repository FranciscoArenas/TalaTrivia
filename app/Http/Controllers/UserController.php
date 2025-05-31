<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::select(['id', 'name', 'email', 'role', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Este método no se implementa aquí ya que el registro se hace via AuthController
        return response()->json([
            'message' => 'Use /api/register para crear nuevos usuarios'
        ], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::select(['id', 'name', 'email', 'role', 'created_at'])
                   ->findOrFail($id);

        // Obtener estadísticas del usuario si es jugador
        $stats = [];
        if ($user->isPlayer()) {
            $stats = [
                'trivias_completed' => $user->assignedTrivias()
                                          ->wherePivot('completed', true)
                                          ->count(),
                'total_score' => $user->assignedTrivias()
                                    ->wherePivot('completed', true)
                                    ->sum('trivia_users.total_score'),
                'trivias_assigned' => $user->assignedTrivias()->count(),
            ];
        }

        return response()->json([
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Validar que solo admins puedan cambiar roles
        if ($request->has('role') && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Solo los administradores pueden cambiar roles'
            ], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:admin,player',
        ]);

        $user->update($validatedData);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $user->fresh(['id', 'name', 'email', 'role'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Evitar que un usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'No puedes eliminar tu propia cuenta'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }
}
