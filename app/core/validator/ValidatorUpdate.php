<?php

namespace App\Validator;

class ValidatorUpdate
{
    private array $errors = [];
    private array $data = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];
        $this->data = $data;

        foreach ($rules as $field => $ruleList) {
            $value = $data[$field] ?? '';

            foreach ($ruleList as $key => $rule) {
                $name = is_int($key) ? $rule : $key;
                $param = is_int($key) ? null : $rule;

                $error = $this->checkRule($name, $field, $value, $param);
                if ($error) {
                    $this->errors[$field][] = $this->format($error, $field, $param);
                }
            }
        }

        return empty($this->errors);
    }

    private function checkRule(string $rule, string $field, $value, $param = null): ?ErrorMessage
    {
        return match ($rule) {
            'required' => empty($value) ? ErrorMessage::REQUIRED : null,
            'email'    => !filter_var($value, FILTER_VALIDATE_EMAIL) ? ErrorMessage::EMAIL : null,
            'min'      => strlen($value) < $param ? ErrorMessage::MIN : null,
            'same'     => $value !== ($this->data[$param] ?? null) ? ErrorMessage::SAME : null,
            default    => null,
        };
    }

    private function format(ErrorMessage $error, string $field, $param = null): string
    {
        return str_replace(
            [':field', ':param'],
            [$field, $param],
            $error->value
        );
    }

    public function errors(): array
    {
        return $this->errors;
    }
}


$validator = new ValidatorUpdate();

$data = [
    'email' => 'exemple@test',
    'password' => '12345',
    'confirm' => '1234',
];

$rules = [
    'email'    => ['required', 'email'],
    'password' => ['required', 'min' => 6],
    'confirm'  => ['same' => 'password'],
];

if ($validator->validate($data, $rules)) {
    echo "✅ Données valides.";
} else {
    echo "❌ Erreurs :\n";
    foreach ($validator->errors() as $msgs) {
        foreach ($msgs as $msg) {
            echo "- $msg\n";
        }
    }
}
