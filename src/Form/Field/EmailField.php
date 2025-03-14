<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class EmailField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class EmailField extends TextField
{
    /**
     * @param string|null $value
     * @return array
     */
    public function validate(?string $value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        return $errors;
    }
}