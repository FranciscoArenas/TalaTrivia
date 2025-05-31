<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeders de TalaTrivia...');

        // Ejecutar seeders en orden específico
        $this->call([
            UserSeeder::class,
            QuestionSeeder::class,
            TriviaSeeder::class,
        ]);

        $this->command->info('✅ Todos los seeders de TalaTrivia completados exitosamente!');
        $this->command->info('📊 Base de datos poblada con:');
        $this->command->info('   - Usuarios administradores y jugadores');
        $this->command->info('   - Preguntas de múltiples categorías y dificultades');
        $this->command->info('   - Trivias de ejemplo con asignaciones de usuarios');
        $this->command->info('🔐 Credenciales de prueba:');
        $this->command->info('   Admin: admin@test.com / password');
        $this->command->info('   Player: juan@test.com / password');
    }
}
