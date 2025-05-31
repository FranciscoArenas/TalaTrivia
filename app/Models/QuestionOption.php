<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="QuestionOption",
 *     type="object",
 *     title="Opción de Pregunta",
 *     required={"id", "question_id", "option_text", "is_correct"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="question_id", type="integer", example=1),
 *     @OA\Property(property="option_text", type="string", example="París"),
 *     @OA\Property(property="is_correct", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The question this option belongs to
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * User answers that selected this option
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Scope to get only correct options
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope to get only incorrect options
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }
}
