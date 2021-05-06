<?php


namespace core;


abstract class FormModel extends Model
{
    const REQUIRED = 'required';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const MIN_LENGTH = 'min_len';
    const MAX_LENGTH = 'max_len';
    const MATCH = 'match';
    const UNIQUE = 'unique';

    public $errors = [];

    // input names used by Form class to create form inputs
    abstract public function formInputs(): array;

    abstract public function validationRules(): array;

    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->formInputs())) {
                $this->$key = $value;
            }
        }
    }

    public function addError(string $inputName, string $message)
    {
        $this->errors[$inputName][] = $message;
    }

    public function addValidationError(string $inputName, $rule)
    {
        $errorMessages = [
            self::REQUIRED => 'This field is required',
            self::EMAIL => 'Invalid email address',
            self::PASSWORD => 'Password must contain at least one lowercase letter, one uppercase letter, one digit and be minimum eight characters long',
            self::MIN_LENGTH => 'This field must be minimum {{ $param }} characters long',
            self::MAX_LENGTH => 'This field must be maximum characters {{ $param }} long',
            self::MATCH => 'This field must match {{ $param }} field',
            self::UNIQUE => 'This {{ $param }} already exists'
        ];

        if (is_string($rule)) {
            $message = $errorMessages[$rule];
        } else {
            // $rule[0] is the rule name, $rule[1] is the parameter
            $message = str_replace('{{ $param }}', $rule[1], $errorMessages[$rule[0]]);
        }

        $this->addError($inputName, $message);
    }

    public function getErrors($inputName)
    {
        if (isset($this->errors[$inputName])) {
            return $this->errors[$inputName];
        }

        return [];
    }

    public function validate()
    {
        foreach ($this->validationRules() as $inputName => $validationRules) {
            $inputValue = $this->$inputName;
            foreach ($validationRules as $rule) {
                $ruleName = $rule;
                if (is_array($ruleName)) {
                    $ruleName = $rule[0];
                }

                if ($ruleName === self::REQUIRED && !$inputValue) {
                    $this->addValidationError($inputName, $rule);
                }

                if ($ruleName === self::EMAIL && !filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
                    $this->addValidationError($inputName, $rule);
                }

                // check if password contains at least one lowercase letter, one uppercase letter,
                // one digit and minimum eight word characters
                if ($ruleName === self::PASSWORD) {
                    if (!preg_match('/(?=[^a-z]*[a-z]+)(?=[^A-Z]*[A-Z]+)(?=\D*\d+)\w{8,}/', $inputValue)) {
                        $this->addValidationError($inputName, $rule);
                    }
                }

                // $rule[1] is the number of the minimum characters allowed
                if ($ruleName === self::MIN_LENGTH && strlen($inputValue) < $rule[1]) {
                    $this->addValidationError($inputName, $rule);
                }

                // $rule[1] is the number of the maximum characters allowed
                if ($ruleName === self::MAX_LENGTH && strlen($inputValue) > $rule[1]) {
                    $this->addValidationError($inputName, $rule);
                }

                if ($ruleName === self::MATCH && $inputValue !== $this->{$rule[1]}) {
                    $this->addValidationError($inputName, $rule);
                }

                if ($ruleName === self::UNIQUE) {
                    if (static::get($inputName, $inputValue)) $this->addValidationError($inputName, $rule);
                }
            }
        }

        return empty($this->errors);
    }
}
