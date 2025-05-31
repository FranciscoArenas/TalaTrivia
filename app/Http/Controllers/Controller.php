<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="TalaTrivia API - Sistema de Trivias Interactivas",
 *     version="1.0.0",
 *     description="API REST para el sistema de trivias TalaTrivia. Permite la gestión completa de usuarios con roles diferenciados (administradores y jugadores), creación y administración de preguntas con múltiples niveles de dificultad, organización de trivias personalizables con asignación de participantes, sistema de respuestas en tiempo real con validación automática, puntuación inteligente basada en dificultad y tiempo de respuesta, rankings dinámicos y estadísticas detalladas de rendimiento. Ideal para empresas, instituciones educativas y plataformas de entretenimiento que buscan implementar sistemas de evaluación gamificados y competencias interactivas.",
 *     @OA\Contact(
 *         name="Francisco Arenas",
 *         email="francisco.arenasp@gmail.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor de desarrollo local"
 * )
 * 
 * @OA\Server(
 *     url="https://api.talatrivia.com",
 *     description="Servidor de producción"
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
 * @OA\Tag(
 *     name="Usuarios",
 *     description="Gestión de usuarios y perfiles de jugador"
 * )
 * 
 * @OA\Tag(
 *     name="Rankings",
 *     description="Consulta de rankings y estadísticas de rendimiento"
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Respuesta de Error",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Mensaje de error"),
 *     @OA\Property(property="errors", type="object", nullable=true, description="Detalles específicos del error"),
 *     @OA\Property(property="code", type="integer", example=400, description="Código de estado HTTP")
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Respuesta Exitosa",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operación exitosa"),
 *     @OA\Property(property="data", type="object", nullable=true, description="Datos de la respuesta"),
 *     @OA\Property(property="meta", type="object", nullable=true, description="Metadatos adicionales como paginación")
 * )
 * 
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     title="Metadatos de Paginación",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=100),
 *     @OA\Property(property="last_page", type="integer", example=7),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="to", type="integer", example=15)
 * )
 * 
 * @OA\Schema(
 *     schema="TriviaRanking",
 *     type="object",
 *     title="Ranking de Trivia",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="user_name", type="string", example="Juan Pérez"),
 *     @OA\Property(property="email", type="string", example="juan.perez@example.com"),
 *     @OA\Property(property="total_score", type="integer", example=15, description="Puntaje total obtenido"),
 *     @OA\Property(property="correct_answers", type="integer", example=5, description="Número de respuestas correctas"),
 *     @OA\Property(property="total_questions", type="integer", example=6, description="Total de preguntas respondidas"),
 *     @OA\Property(property="accuracy", type="number", format="float", example=83.33, description="Porcentaje de precisión"),
 *     @OA\Property(property="completion_time", type="string", format="date-time", description="Tiempo de finalización"),
 *     @OA\Property(property="rank", type="integer", example=1, description="Posición en el ranking")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Error de Validación",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Los datos proporcionados no son válidos"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         example={
 *             "email": {"El campo email es obligatorio."},
 *             "password": {"La contraseña debe tener al menos 8 caracteres."}
 *         }
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}