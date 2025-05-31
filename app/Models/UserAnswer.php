<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'trivia_id',
        'user_id',
        'question_id',
        'question_option_id',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The trivia this answer belongs to
     */
    public function trivia()
    {
        return $this->belongsTo(Trivia::class);
    }

    /**
     * The user who gave this answer
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The question this answer is for
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The selected option
     */
    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }

    /**
     * Scope to filter by trivia
     */
    public function scopeByTrivia($query, int $triviaId)
    {
        return $query->where('trivia_id', $triviaId);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get only correct answers
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope to get only incorrect answers
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }
}
