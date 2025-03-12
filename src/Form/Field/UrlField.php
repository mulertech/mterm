<?php

namespace MulerTech\MTerm\Form\Field;

class UrlField extends TextField
{
    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $errors[] = "Please enter a valid URL.";
        }

        return $errors;
    }
}