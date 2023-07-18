<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HostWhitelist implements ValidationRule
{
    public function __construct(private readonly array $whitelist) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $host = parse_url($value, PHP_URL_HOST);
        if (!in_array($host, $this->whitelist)) {
            $fail('The host »' . $host . '« is not on the whitelist');
        }
    }
}
