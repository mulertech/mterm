<?php

namespace MulerTech\MTerm\Form\Validator;

interface ValidatorInterface
{
    /**
     * Validate a value
     *
     * @param mixed $value Value to validate
     * @return string|null Error message or null if valid
     */
    public function validate($value): ?string;
}