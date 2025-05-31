<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trivia;
use App\Models\Question;
use App\Models\User;

class TriviaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar que tengamos preguntas y usuarios
        $questions = Question::all();
        $players = User::where('role', 'player')->get();

        if ($questions->isEmpty() || $players->isEmpty()) {
            $this->command->warn('Se necesitan preguntas y usuarios para crear trivias. Ejecutar UserSeeder y QuestionSeeder primero.');
            return;
        }

        // Trivia 1: Historia General
        $triviaHistoria = Trivia::create([
            'name' => 'Historia General',
            'description' => 'Una trivia completa sobre eventos históricos importantes, desde la antigüedad hasta la era moderna.',
            'status' => 'active',
            'created_by' => 1, // Admin user ID
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(10),
        ]);

        // Agregar preguntas de historia a la trivia (preguntas 1-4)
        $historyQuestions = $questions->slice(0, 4);
        foreach ($historyQuestions as $question) {
            $triviaHistoria->questions()->attach($question->id);
        }

        // Agregar algunos jugadores a la trivia
        $triviaHistoria->users()->attach($players->take(6)->pluck('id'));

        // Trivia 2: Ciencia y Tecnología
        $triviaCiencia = Trivia::create([
            'name' => 'Ciencia y Tecnología',
            'description' => 'Desafía tus conocimientos sobre ciencia, física, química y los últimos avances tecnológicos.',
            'status' => 'active',
            'created_by' => 1, // Admin user ID
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->addDays(15),
        ]);

        // Agregar preguntas de ciencia a la trivia (preguntas 5-8)
        $scienceQuestions = $questions->slice(4, 4);
        foreach ($scienceQuestions as $question) {
            $triviaCiencia->questions()->attach($question->id);
        }

        // Agregar algunos jugadores a la trivia
        $triviaCiencia->users()->attach($players->skip(2)->take(8)->pluck('id'));

        // Trivia 3: Deportes Extremos
        $triviaDeportes = Trivia::create([
            'name' => 'Deportes Extremos',
            'description' => 'Todo sobre deportes, olimpiadas, récords mundiales y los atletas más famosos de la historia.',
            'status' => 'active',
            'created_by' => 1, // Admin user ID
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(20),
        ]);

        // Agregar preguntas de deportes a la trivia (preguntas 9-12)
        $sportsQuestions = $questions->slice(8, 4);
        foreach ($sportsQuestions as $question) {
            $triviaDeportes->questions()->attach($question->id);
        }

        // Agregar algunos jugadores a la trivia
        $triviaDeportes->users()->attach($players->skip(1)->take(7)->pluck('id'));

        // Trivia 4: Conocimiento General
        $triviaGeneral = Trivia::create([
            'name' => 'Conocimiento General',
            'description' => 'Una mezcla de preguntas de diferentes categorías para poner a prueba tu cultura general.',
            'status' => 'active',
            'created_by' => 1, // Admin user ID
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        // Agregar preguntas variadas (seleccionar algunas aleatorias)
        $generalQuestions = $questions->slice(0, 10);

        foreach ($generalQuestions as $question) {
            $triviaGeneral->questions()->attach($question->id);
        }

        // Agregar todos los jugadores a esta trivia
        $triviaGeneral->users()->attach($players->pluck('id'));

        // Trivia 5: Geografía Mundial
        $triviaGeografia = Trivia::create([
            'name' => 'Geografía Mundial',
            'description' => 'Explora el mundo a través de preguntas sobre países, capitales, ríos y montañas.',
            'status' => 'draft', // Trivia inactiva para pruebas
            'created_by' => 1, // Admin user ID
            'starts_at' => now()->addDays(5),
            'ends_at' => now()->addDays(25),
        ]);

        // Agregar preguntas de geografía a la trivia (preguntas 13-15)
        $geographyQuestions = $questions->slice(12, 3);
        foreach ($geographyQuestions as $question) {
            $triviaGeografia->questions()->attach($question->id);
        }

        // Agregar algunos jugadores a la trivia
        $triviaGeografia->users()->attach($players->skip(3)->take(5)->pluck('id'));

        // Trivia 6: Entretenimiento y Cultura Pop
        $triviaEntretenimiento = Trivia::create([
            'name' => 'Entretenimiento y Cultura Pop',
            'description' => 'Preguntas sobre películas, música, series de TV y cultura popular contemporánea.',
            'status' => 'active',
            'created_by' => 1, // Admin user ID
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->addDays(12),
        ]);

        // Agregar preguntas de entretenimiento a la trivia (preguntas 16-18)
        $entertainmentQuestions = $questions->slice(15, 3);
        foreach ($entertainmentQuestions as $question) {
            $triviaEntretenimiento->questions()->attach($question->id);
        }

        // Agregar algunos jugadores a la trivia
        $triviaEntretenimiento->users()->attach($players->skip(4)->take(6)->pluck('id'));

        $this->command->info('✅ Trivias creadas exitosamente:');
        $this->command->info('   - Historia General (' . $historyQuestions->count() . ' preguntas)');
        $this->command->info('   - Ciencia y Tecnología (' . $scienceQuestions->count() . ' preguntas)');
        $this->command->info('   - Deportes Extremos (' . $sportsQuestions->count() . ' preguntas)');
        $this->command->info('   - Conocimiento General (' . $generalQuestions->count() . ' preguntas)');
        $this->command->info('   - Geografía Mundial (' . $geographyQuestions->count() . ' preguntas)');
        $this->command->info('   - Entretenimiento y Cultura Pop (' . $entertainmentQuestions->count() . ' preguntas)');
    }
}
