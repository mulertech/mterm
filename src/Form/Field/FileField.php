<?php

namespace MulerTech\MTerm\Form\Field;

class FileField extends AbstractField
{
    private array $allowedExtensions = [];
    private ?int $maxSize = null;

    public function setAllowedExtensions(array $extensions): self
    {
        $this->allowedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    public function setMaxSize(int $bytes): self
    {
        $this->maxSize = $bytes;
        return $this;
    }

    public function processInput($input)
    {
        if ($input === '') {
            return $this->defaultValue;
        }

        return $input;
    }

    public function validate($value): array
    {
        $errors = parent::validate($value);

        if ($value !== null && $value !== '') {
            if (!file_exists($value)) {
                $errors[] = "File not found: {$value}";
                return $errors;
            }

            if (!empty($this->allowedExtensions)) {
                $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                if (!in_array($extension, $this->allowedExtensions, true)) {
                    $allowed = implode(', ', $this->allowedExtensions);
                    $errors[] = "File type not allowed. Allowed types: {$allowed}";
                }
            }

            if ($this->maxSize !== null) {
                $fileSize = filesize($value);
                if ($fileSize > $this->maxSize) {
                    $maxSizeMb = number_format($this->maxSize / 1048576, 2);
                    $errors[] = "File is too large. Maximum size is {$maxSizeMb} MB.";
                }
            }
        }

        return $errors;
    }
}