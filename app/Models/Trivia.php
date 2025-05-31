<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *     schema="Trivia",
 *     type="object",
 *     title="Trivia",
 *     required={"id", "name", "description", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Trivia de Historia"),
 *     @OA\Property(property="description", type="string", example="Una trivia sobre historia mundial"),
 *     @OA\Property(property="status", type="string", enum={"draft", "active", "completed"}, example="active"),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(property="starts_at", type="string", format="date-time", example="2023-01-01T10:00:00Z"),
 *     @OA\Property(property="ends_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Trivia extends Model
{
    use HasFactory;

    protected $table = 'trivias';

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The user who created this trivia
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Questions assigned to this trivia
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'trivia_questions')
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderBy('trivia_questions.order');
    }

    /**
     * Users assigned to this trivia
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'trivia_users')
                    ->withPivot('total_score', 'completed', 'started_at', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * User answers for this trivia
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Get ranking of users for this trivia
     */
    public function getUserRanking()
    {
        return $this->users()
                    ->wherePivot('completed', true)
                    ->orderByDesc('trivia_users.total_score')
                    ->select(['users.*', 'trivia_users.total_score', 'trivia_users.completed_at'])
                    ->get();
    }

    /**
     * Get user participation data
     */
    public function getUserParticipation(int $userId)
    {
        return $this->users()
                    ->where('users.id', $userId)
                    ->first();
    }

    /**
     * Check if user can participate in this trivia
     */
    public function canUserParticipate(int $userId): bool
    {
        // Check if user is assigned to this trivia
        if (!$this->users()->where('users.id', $userId)->exists()) {
            return false;
        }

        // Check if trivia is active
        if ($this->status !== 'active') {
            return false;
        }

        // Check if user has already completed
        $participation = $this->getUserParticipation($userId);
        return !$participation || !$participation->pivot->completed;
    }

    /**
     * Calculate total possible score for this trivia
     */
    public function getTotalPossibleScore(): int
    {
        return $this->questions()->sum(DB::raw("
            CASE
                WHEN questions.difficulty = 'easy' THEN 1
                WHEN questions.difficulty = 'medium' THEN 2
                WHEN questions.difficulty = 'hard' THEN 3
                ELSE 1
            END
        "));
    }

    /**
     * Get questions with their options (without revealing correct answers)
     */
    public function getQuestionsForPlayer()
    {
        return $this->questions()
                    ->with(['options' => function($query) {
                        $query->select('id', 'question_id', 'option_text');
                    }])
                    ->get(['id', 'question', 'difficulty']);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by creator
     */
    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope for active trivias
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
