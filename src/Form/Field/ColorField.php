<?php

namespace MulerTech\MTerm\Form\Field;

class ColorField extends TextField
{
    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            // Validate hexadecimal color format (#RRGGBB or #RGB)
            if (!preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $value)) {
                $errors[] = "Please enter a valid color in hexadecimal format (#RGB or #RRGGBB).";
            }
        }

        return $errors;
    }
}