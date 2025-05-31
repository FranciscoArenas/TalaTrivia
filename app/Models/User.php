<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is player
     */
    public function isPlayer(): bool
    {
        return $this->role === 'player';
    }

    /**
     * Trivias created by this user (admin only)
     */
    public function createdTrivias()
    {
        return $this->hasMany(Trivia::class, 'created_by');
    }

    /**
     * Trivias assigned to this user (player)
     */
    public function assignedTrivias()
    {
        return $this->belongsToMany(Trivia::class, 'trivia_users')
                    ->withPivot('total_score', 'completed', 'started_at', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * User answers in trivias
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Questions created by this user (admin only)
     */
    public function createdQuestions()
    {
        return $this->hasMany(Question::class, 'created_by');
    }
}
