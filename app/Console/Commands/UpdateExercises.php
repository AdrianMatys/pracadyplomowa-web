<?php

namespace App\Console\Commands;

use App\Models\Exercise;
use Illuminate\Console\Command;

class UpdateExercises extends Command
{
    protected $signature = 'exercises:update-validation';

    protected $description = 'Update validation logic for existing exercises (React regex, JS ID)';

    public function handle()
    {
        $updates = [
            'Licznik kliknięć' => ['judge0_language_id' => 63],
            'Kwadraty w pionie' => ['judge0_language_id' => 63],
            'Zmiana tekstu' => ['judge0_language_id' => 63],
            'Ukrywanie elementu' => ['judge0_language_id' => 63],
            'Zmiana koloru tła' => ['judge0_language_id' => 63],

            'Warunek if' => ['judge0_language_id' => 63],
            'Pętla for' => ['judge0_language_id' => 63],
            'Komponent React' => [
                'validation_regex' => '/return\\s*\\(\\s*<h1>Hello React<\\/h1>\\s*\\)/',
                'expected_output' => null,
                'judge0_language_id' => null,
            ],
            'Stan licznika' => [
                'validation_regex' => '/setCount\\s*\\(\\s*count\\s*\\+\\s*1\\s*\\)/',
                'expected_output' => null,
                'judge0_language_id' => null,
            ],
            'Tablica imion' => ['judge0_language_id' => 63],

            'Suma liczb' => ['judge0_language_id' => 63],
            'Endpoint status' => [
                'validation_regex' => '/res\\.send\\(\\s*[\'"]OK[\'"]\\s*\\)/',
                'expected_output' => null,
                'judge0_language_id' => null,
            ],
            'Pobieranie użytkowników' => [
                'validation_regex' => '/SELECT\\s+\\*\\s+FROM\\s+users/i',
                'expected_output' => null,
                'judge0_language_id' => null,
            ],
            'Czy nie puste?' => [
                'validation_regex' => '/!empty\\(\\s*\\$name\\s*\\)/',
                'expected_output' => null,
                'judge0_language_id' => null,
            ],
            'Panel admina' => ['judge0_language_id' => 63],
        ];

        foreach ($updates as $title => $data) {
            $exercise = Exercise::where('title', $title)->first();
            if ($exercise) {
                $exercise->update($data);
                $this->info("Updated: $title");
            } else {
                $this->warn("Exercise not found: $title");
            }
        }

        $this->info('Validation update complete.');
    }
}
