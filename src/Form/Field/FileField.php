<?php

namespace MulerTech\MTerm\Form\Field;

/**
 * Class FileField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class FileField extends AbstractField
{
    /** @var array<string> */
    private array $allowedExtensions = [];
    private ?int $maxSize = null;

    /**
     * @param array<string> $extensions
     * @return $this
     */
    public function setAllowedExtensions(array $extensions): self
    {
        $this->allowedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * @param int $bytes
     * @return $this
     */
    public function setMaxSize(int $bytes): self
    {
        $this->maxSize = $bytes;
        return $this;
    }

    /**
     * @param string $input
     * @return string|int|float|array<int|string, string>
     */
    public function processInput(string $input): string|int|float|array
    {
        if ($input === '' && !is_null($this->defaultValue)) {
            return $this->defaultValue;
        }

        return $input;
    }

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
            if (!file_exists($value)) {
                $errors[] = "File not found: $value";
                return $errors;
            }

            if (!empty($this->allowedExtensions)) {
                $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                if (!in_array($extension, $this->allowedExtensions, true)) {
                    $allowed = implode(', ', $this->allowedExtensions);
                    $errors[] = "File type not allowed. Allowed types: $allowed";
                }
            }

            if ($this->maxSize !== null) {
                $fileSize = filesize($value);
                if ($fileSize > $this->maxSize) {
                    $maxSizeMb = number_format($this->maxSize / 1048576, 2);
                    $errors[] = "File is too large. Maximum size is $maxSizeMb MB.";
                }
            }
        }

        return $errors;
    }
}
