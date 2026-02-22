<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class GuestCourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['title' => 'Darmowy kurs dla gości'],
            [
                'description' => 'Spróbuj swoich sił w programowaniu bez logowania! 4 proste zadania z różnych dziedzin IT.',
                'reward' => 0,
                'duration' => 15,
                'level' => 'junior',
                'image_path' => 'courses/guest.png', 
            ]
        );

        $lessons = [
            [
                'title' => 'Frontend: Twój pierwszy nagłówek',
                'content' => "HTML to język, który buduje strony internetowe. Tagi (znaczniki) mówią przeglądarce, co wyświetlić.\n\n`<h1>` to Najważniejszy Nagłówek.\n\nTwoim zadaniem jest stworzenie nagłówka H1 z napisem \"Witaj Świecie\".",
                'exercise' => [
                    'title' => 'Nagłówek H1',
                    'description' => 'Stwórz nagłówek h1 z tekstem "Witaj Świecie".',
                    'initial_code' => "<!-- Napisz kod HTML poniżej -->\n",
                    'expected_output' => '<h1>Witaj Świecie</h1>',
                    'validation_regex' => '/<h1>\s*Witaj Świecie\s*<\/h1>/i',
                    'hint' => 'Nagłówek pierwszego poziomu otacza tekst znacznikami otwierającym i zamykającym. Jaki tag odpowiada za nagłówek H1?',
                    'hint_2' => 'Wzorzec: <h1>Twój tekst tutaj</h1> — wstaw odpowiednią treść między znaczniki.',
                    'judge0_language_id' => null, // HTML check via regex/preview
                    'preview_type' => 'html'
                ]
            ],
            [
                'title' => 'Backend (PHP): Powitanie',
                'content' => "PHP to język, który działa na serwerze. Komenda `echo` służy do wypisywania tekstu.\n\nKażda instrukcja w PHP musi kończyć się średnikiem `;`.",
                'exercise' => [
                    'title' => 'Echo w PHP',
                    'description' => 'Użyj komendy echo, aby wypisać tekst "Działam!". Pamiętaj o cudzysłowach i średniku.',
                    'initial_code' => "<?php\n// Wypisz tekst poniżej\n",
                    'expected_output' => 'Działam!',
                    'validation_regex' => '/echo\s*[\'"]Działam![\'"]\s*;/i',
                    'hint' => 'Komenda echo wypisuje tekst na ekran. Tekst musi być otoczony cudzysłowami, a instrukcja kończy się średnikiem.',
                    'judge0_language_id' => 68, // PHP
                    'hint_2' => 'Wzorzec: echo "Twój tekst"; — wstaw odpowiednią treść między cudzysłowy.'
                ]
            ],
            [
                'title' => 'Baza danych (SQL): Pobieranie danych',
                'content' => "SQL służy do rozmowy z bazą danych. `SELECT * FROM tabela` oznacza \"Pobierz wszystko z tabeli\".\n\nMamy tabelę `users`. Pobierz z niej wszystkie dane.",
                'exercise' => [
                    'title' => 'SELECT *',
                    'description' => 'Napisz zapytanie, które pobierze wszystkie dane z tabeli users.',
                    'initial_code' => "-- Wybierz wszystko z tabeli users\nSELECT ...",
                    'expected_output' => null,
                    'validation_regex' => '/SELECT\s+\*\s+FROM\s+users/i',
                    'hint' => 'Gwiazdka (*) w SQL oznacza "wszystkie kolumny". Po FROM podaj nazwę tabeli.',
                    'judge0_language_id' => null, // SQL via regex mostly here or mock
                    'hint_2' => 'Wzorzec: SELECT [co] FROM [skąd]; — zastąp [co] i [skąd] odpowiednimi wartościami.'
                ]
            ],
            [
                'title' => 'Logika (JavaScript): Konsola',
                'content' => "JavaScript ożywia strony. `console.log()` służy do wyświetlania wiadomości w konsoli programisty.\n\nWypisz liczbę 100 w konsoli.",
                'exercise' => [
                    'title' => 'Liczba w konsoli',
                    'description' => 'Użyj console.log(), aby wypisać liczbę 100.',
                    'initial_code' => "// Wypisz 100\n",
                    'expected_output' => '100',
                    'validation_regex' => '/console\.log\(\s*100\s*\)/',
                    'hint' => 'Funkcja console.log() wypisuje wartość w konsoli. Liczbę podajesz bezpośrednio jako argument, bez cudzysłowów.',
                    'judge0_language_id' => 63, // JS
                    'hint_2' => 'Wzorzec: console.log([wartość]); — wstaw odpowiednią liczbę w miejsce [wartość].'
                ]
            ]
        ];

        foreach ($lessons as $index => $data) {
            $lesson = Lesson::create([
                'course_id' => $course->id,
                'title' => $data['title'],
                'content' => $data['content'],
                'order' => $index,
            ]);

            Exercise::create([
                'lesson_id' => $lesson->id,
                'title' => $data['exercise']['title'],
                'description' => $data['exercise']['description'],
                'initial_code' => $data['exercise']['initial_code'],
                'expected_output' => $data['exercise']['expected_output'],
                'validation_regex' => $data['exercise']['validation_regex'],
                'judge0_language_id' => $data['exercise']['judge0_language_id'],
                'preview_type' => $data['exercise']['preview_type'] ?? 'none',
                'hint' => $data['exercise']['hint'],
                'hint_2' => $data['exercise']['hint_2'] ?? null,
            ]);
        }
    }
}
