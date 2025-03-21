<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class ColorField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class ColorField extends TextField
{
    /**
     * @param string|int|float|array<int|string, string>|null $value
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        $errors = parent::validate($value);

        if ($value === '') {
            return $errors;
        }

        if (is_string($value)) {
            // Validate hexadecimal color format (#RRGGBB or #RGB)
            if (!preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $value)) {
                $errors[] = "Please enter a valid color in hexadecimal format (#RGB or #RRGGBB).";
            }
        }

        return $errors;
    }
}
