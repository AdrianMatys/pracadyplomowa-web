<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NicknameNotProfane implements ValidationRule
{
    private static array $blockedWords = [
        'kutas', 'chuj', 'pizda', 'kurwa', 'jebany', 'jebac', 'pierdol',
        'skurwiel', 'cwel', 'pedal', 'cipa', 'fiut', 'dupek', 'dupe',
        'ass', 'fuck', 'shit', 'bitch', 'cunt', 'dick', 'cock', 'pussy',
        'nigger', 'nigga', 'faggot', 'whore', 'slut',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = mb_strtolower((string) $value);
        $normalized = str_replace(['0', '1', '3', '4', '@', '$'], ['o', 'i', 'e', 'a', 'a', 's'], $normalized);
        $normalized = preg_replace('/[^a-z0-9]/', '', $normalized);

        foreach (self::$blockedWords as $word) {
            if (str_contains($normalized, $word)) {
                $fail('Nazwa użytkownika zawiera niedozwolone słowa.');
                return;
            }
        }
    }
}
