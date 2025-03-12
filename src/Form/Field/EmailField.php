<?php

namespace MulerTech\MTerm\Form\Field;

class EmailField extends TextField
{
    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        return $errors;
    }
}