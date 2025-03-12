<?php

namespace MulerTech\MTerm\Form\Field;

use DateTime;

class DateField extends TextField
{
    private string $format = 'Y-m-d';

    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function processInput($input)
    {
        if ($input === '') {
            return $this->defaultValue;
        }

        $date = DateTime::createFromFormat($this->format, $input);
        if ($date === false) {
            return $input; // Return raw input for validation to catch error
        }

        return $date->format($this->format);
    }

    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            $date = DateTime::createFromFormat($this->format, $value);

            $dateErrors = DateTime::getLastErrors();

            if ($date === false || $dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0) {
                $errors[] = "Please enter a valid date in {$this->format} format.";
            }
        }

        return $errors;
    }
}