<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin users
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@tala-trivia.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin Usuario',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create player users
        $players = [
            ['name' => 'Juan Pérez', 'email' => 'juan@test.com'],
            ['name' => 'María García', 'email' => 'maria@test.com'],
            ['name' => 'Carlos López', 'email' => 'carlos@test.com'],
            ['name' => 'Ana Martínez', 'email' => 'ana@test.com'],
            ['name' => 'Luis Rodríguez', 'email' => 'luis@test.com'],
            ['name' => 'Carmen Sánchez', 'email' => 'carmen@test.com'],
            ['name' => 'Diego Fernández', 'email' => 'diego@test.com'],
            ['name' => 'Laura González', 'email' => 'laura@test.com'],
            ['name' => 'Miguel Torres', 'email' => 'miguel@test.com'],
            ['name' => 'Sofia Ruiz', 'email' => 'sofia@test.com'],
        ];

        foreach ($players as $player) {
            User::create([
                'name' => $player['name'],
                'email' => $player['email'],
                'password' => Hash::make('password'),
                'role' => 'player',
                'email_verified_at' => now(),
            ]);
        }

        // Create additional random players using factory if available
        // User::factory(20)->create(['role' => 'player']);
    }
}
