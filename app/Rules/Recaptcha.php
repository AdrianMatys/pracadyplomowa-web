<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    public function __construct(private string $action = 'login') {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('recaptcha.secret_key');
        $minScore = config('recaptcha.min_score', 0.1);

        if (! $secretKey || App::runningUnitTests()) {
            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $value,
        ]);

        $result = $response->json();

        if (! ($result['success'] ?? false) || ($result['score'] ?? 0) < $minScore || ($result['action'] ?? '') !== $this->action) {
            \Illuminate\Support\Facades\Log::warning('reCAPTCHA validation failed', [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? 0,
                'action' => $result['action'] ?? '',
                'expected_action' => $this->action,
                'hostname' => $result['hostname'] ?? '',
                'error-codes' => $result['error-codes'] ?? [],
            ]);
            $fail('Weryfikacja reCAPTCHA nie powiodła się. Spróbuj ponownie.');
        }
    }
}
