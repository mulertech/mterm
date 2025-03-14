<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Class EmailValidator
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class EmailValidator extends AbstractValidator
{
    /**
     * @param string $errorMessage
     */
    public function __construct(string $errorMessage = "Please enter a valid email address.")
    {
        parent::__construct($errorMessage);
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function validate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->errorMessage;
        }

        return null;
    }
}