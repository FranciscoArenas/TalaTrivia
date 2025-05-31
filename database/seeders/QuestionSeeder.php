<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionOption;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // Historia - Fácil
            [
                'question' => '¿En qué año comenzó la Segunda Guerra Mundial?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => '1939', 'is_correct' => true],
                    ['text' => '1940', 'is_correct' => false],
                    ['text' => '1941', 'is_correct' => false],
                    ['text' => '1938', 'is_correct' => false],
                ]
            ],
            [
                'question' => '¿Quién fue el primer presidente de Estados Unidos?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'George Washington', 'is_correct' => true],
                    ['text' => 'Thomas Jefferson', 'is_correct' => false],
                    ['text' => 'John Adams', 'is_correct' => false],
                    ['text' => 'Benjamin Franklin', 'is_correct' => false],
                ]
            ],

            // Historia - Medio
            [
                'question' => '¿En qué año cayó el Muro de Berlín?',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '1987', 'is_correct' => false],
                    ['text' => '1989', 'is_correct' => true],
                    ['text' => '1991', 'is_correct' => false],
                    ['text' => '1985', 'is_correct' => false],
                ]
            ],

            // Historia - Difícil
            [
                'question' => '¿Qué tratado puso fin oficialmente a la Primera Guerra Mundial?',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Tratado de Versalles', 'is_correct' => true],
                    ['text' => 'Tratado de París', 'is_correct' => false],
                    ['text' => 'Tratado de Viena', 'is_correct' => false],
                    ['text' => 'Tratado de Ginebra', 'is_correct' => false],
                ]
            ],

            // Ciencia - Fácil
            [
                'question' => '¿Cuál es el símbolo químico del oro?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Go', 'is_correct' => false],
                    ['text' => 'Au', 'is_correct' => true],
                    ['text' => 'Ag', 'is_correct' => false],
                    ['text' => 'Or', 'is_correct' => false],
                ]
            ],
            [
                'question' => '¿Cuántos planetas hay en el sistema solar?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => '7', 'is_correct' => false],
                    ['text' => '8', 'is_correct' => true],
                    ['text' => '9', 'is_correct' => false],
                    ['text' => '10', 'is_correct' => false],
                ]
            ],

            // Ciencia - Medio
            [
                'question' => '¿Cuál es la velocidad de la luz en el vacío?',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '300,000 km/s', 'is_correct' => false],
                    ['text' => '299,792,458 m/s', 'is_correct' => true],
                    ['text' => '350,000 km/s', 'is_correct' => false],
                    ['text' => '280,000 km/s', 'is_correct' => false],
                ]
            ],

            // Ciencia - Difícil
            [
                'question' => '¿Qué partícula subatómica fue descubierta en el CERN en 2012?',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Bosón de Higgs', 'is_correct' => true],
                    ['text' => 'Quark top', 'is_correct' => false],
                    ['text' => 'Neutrino tau', 'is_correct' => false],
                    ['text' => 'Gluón', 'is_correct' => false],
                ]
            ],

            // Deportes - Fácil
            [
                'question' => '¿Cada cuántos años se celebran los Juegos Olímpicos?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => '2 años', 'is_correct' => false],
                    ['text' => '3 años', 'is_correct' => false],
                    ['text' => '4 años', 'is_correct' => true],
                    ['text' => '5 años', 'is_correct' => false],
                ]
            ],
            [
                'question' => '¿En qué deporte se usa un puck?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Fútbol', 'is_correct' => false],
                    ['text' => 'Basketball', 'is_correct' => false],
                    ['text' => 'Hockey', 'is_correct' => true],
                    ['text' => 'Tennis', 'is_correct' => false],
                ]
            ],

            // Deportes - Medio
            [
                'question' => '¿En qué año se celebraron los primeros Juegos Olímpicos modernos?',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '1892', 'is_correct' => false],
                    ['text' => '1896', 'is_correct' => true],
                    ['text' => '1900', 'is_correct' => false],
                    ['text' => '1888', 'is_correct' => false],
                ]
            ],

            // Deportes - Difícil
            [
                'question' => '¿Cuál es el récord mundial de los 100 metros planos masculinos?',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => '9.58 segundos', 'is_correct' => true],
                    ['text' => '9.63 segundos', 'is_correct' => false],
                    ['text' => '9.69 segundos', 'is_correct' => false],
                    ['text' => '9.72 segundos', 'is_correct' => false],
                ]
            ],

            // Geografía - Fácil
            [
                'question' => '¿Cuál es la capital de Francia?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Lyon', 'is_correct' => false],
                    ['text' => 'Marsella', 'is_correct' => false],
                    ['text' => 'París', 'is_correct' => true],
                    ['text' => 'Toulouse', 'is_correct' => false],
                ]
            ],

            // Geografía - Medio
            [
                'question' => '¿Cuál es el río más largo del mundo?',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Río Amazonas', 'is_correct' => true],
                    ['text' => 'Río Nilo', 'is_correct' => false],
                    ['text' => 'Río Yangtsé', 'is_correct' => false],
                    ['text' => 'Río Misisipi', 'is_correct' => false],
                ]
            ],

            // Geografía - Difícil
            [
                'question' => '¿Cuál es el punto más profundo de los océanos?',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Fosa de las Marianas', 'is_correct' => true],
                    ['text' => 'Fosa de Puerto Rico', 'is_correct' => false],
                    ['text' => 'Fosa de Filipinas', 'is_correct' => false],
                    ['text' => 'Fosa de Japón', 'is_correct' => false],
                ]
            ],

            // Entretenimiento - Fácil
            [
                'question' => '¿Quién dirigió la película "Titanic"?',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Steven Spielberg', 'is_correct' => false],
                    ['text' => 'James Cameron', 'is_correct' => true],
                    ['text' => 'Christopher Nolan', 'is_correct' => false],
                    ['text' => 'Martin Scorsese', 'is_correct' => false],
                ]
            ],

            // Entretenimiento - Medio
            [
                'question' => '¿En qué año se estrenó la primera película de "Star Wars"?',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '1975', 'is_correct' => false],
                    ['text' => '1977', 'is_correct' => true],
                    ['text' => '1979', 'is_correct' => false],
                    ['text' => '1980', 'is_correct' => false],
                ]
            ],

            // Entretenimiento - Difícil
            [
                'question' => '¿Qué película ganó el Oscar a Mejor Película en 1994?',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Forrest Gump', 'is_correct' => true],
                    ['text' => 'Pulp Fiction', 'is_correct' => false],
                    ['text' => 'The Shawshank Redemption', 'is_correct' => false],
                    ['text' => 'Quiz Show', 'is_correct' => false],
                ]
            ],
        ];

        foreach ($questions as $questionData) {
            $question = Question::create([
                'question' => $questionData['question'],
                'difficulty' => $questionData['difficulty'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'],
                    'is_correct' => $optionData['is_correct'],
                ]);
            }
        }
    }
}
