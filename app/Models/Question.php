<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Question",
 *     type="object",
 *     title="Pregunta",
 *     required={"id", "question", "difficulty"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="question", type="string", example="¿Cuál es la capital de Francia?"),
 *     @OA\Property(property="difficulty", type="string", enum={"easy", "medium", "hard"}, example="easy"),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(
 *         property="options",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/QuestionOption")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'difficulty',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The user who created this question
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Options for this question
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Get the correct option for this question
     */
    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    /**
     * Trivias that include this question
     */
    public function trivias()
    {
        return $this->belongsToMany(Trivia::class, 'trivia_questions')
                    ->withPivot('order')
                    ->withTimestamps();
    }

    /**
     * User answers for this question
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Get points for this question based on difficulty
     */
    public function getPointsAttribute(): int
    {
        return match($this->difficulty) {
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            default => 1,
        };
    }

    /**
     * Check if question has a correct answer defined
     */
    public function hasCorrectAnswer(): bool
    {
        return $this->options()->where('is_correct', true)->exists();
    }

    /**
     * Scope to filter by difficulty
     */
    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope to filter by creator
     */
    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }
}
