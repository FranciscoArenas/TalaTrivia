<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
