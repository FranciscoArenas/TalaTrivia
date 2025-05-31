<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Talana TalaTrivia API",
 *     version="2.0.0",
 *     description="API RESTful para el sistema de trivia TalaTrivia desarrollado por Talana. Esta API permite gestionar usuarios, crear preguntas con diferentes niveles de dificultad, organizar trivias y gestionar la participación de jugadores con sistemas de puntuación y ranking en tiempo real.",
 *     @OA\Contact(
 *         name="Equipo Talana",
 *         email="tech@talana.com",
 *         url="https://talana.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor de desarrollo local"
 * )
 * 
 * @OA\Server(
 *     url="http://localhost",
 *     description="Servidor alternativo local"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Autenticación Bearer Token - Formato: Bearer {token}"
 * )
 * 
 * @OA\Tag(
 *     name="Autenticación",
 *     description="Endpoints para registro, login y gestión de sesiones de usuario"
 * )
 * 
 * @OA\Tag(
 *     name="Preguntas",
 *     description="Gestión de preguntas de trivia con diferentes niveles de dificultad"
 * )
 * 
 * @OA\Tag(
 *     name="Trivias",
 *     description="Creación y administración de trivias con preguntas y participantes"
 * )
 * 
 * @OA\Tag(
 *     name="Participación en Trivias",
 *     description="Endpoints para que los jugadores participen en trivias y respondan preguntas"
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Respuesta de Error",
 *     @OA\Property(property="message", type="string", example="Mensaje de error"),
 *     @OA\Property(property="errors", type="object", nullable=true, description="Detalles específicos del error")
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Respuesta Exitosa",
 *     @OA\Property(property="message", type="string", example="Operación exitosa"),
 *     @OA\Property(property="data", type="object", nullable=true, description="Datos de la respuesta")
 * )
 * 
 * @OA\Schema(
 *     schema="TriviaRanking",
 *     type="object",
 *     title="Ranking de Trivia",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="user_name", type="string", example="Juan Pérez"),
 *     @OA\Property(property="total_score", type="integer", example=15, description="Puntaje total obtenido"),
 *     @OA\Property(property="correct_answers", type="integer", example=5, description="Número de respuestas correctas"),
 *     @OA\Property(property="total_questions", type="integer", example=6, description="Total de preguntas respondidas"),
 *     @OA\Property(property="completion_time", type="string", format="date-time", description="Tiempo de finalización"),
 *     @OA\Property(property="rank", type="integer", example=1, description="Posición en el ranking")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}