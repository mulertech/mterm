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
     * @param string|int|float|array<int|string, string>|null $value
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        $errors = parent::validate($value);

        if ($value === '') {
            return $errors;
        }

        if (is_string($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        return $errors;
    }
}
