<?php

namespace MulerTech\MTerm\Form\Validator;

/**
 * Interface ValidatorInterface
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
interface ValidatorInterface
{
    /**
     * Validate a value
     *
     * @param mixed $value Value to validate
     * @return string|null Error message or null if valid
     */
    public function validate(mixed $value): ?string;
}
