<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class UrlField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class UrlField extends TextField
{
    /**
     * @param string|null $value
     * @return array
     */
    public function validate(?string $value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $errors[] = "Please enter a valid URL.";
        }

        return $errors;
    }
}