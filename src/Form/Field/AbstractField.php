<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Form\Validator\ValidatorInterface;

abstract class AbstractField implements FieldInterface
{
    protected string $name;
    protected string $label;
    protected ?string $description = null;
    protected bool $required = false;
    protected $defaultValue = null;
    protected array $validators = [];

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function setDefault($defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function getDefault()
    {
        return $this->defaultValue;
    }

    public function addValidator(ValidatorInterface $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function validate($value): array
    {
        $errors = [];

        // Check required constraint
        if ($this->required && ($value === null || $value === '')) {
            $errors[] = "This field is required.";
            return $errors;
        }

        // Run all validators
        foreach ($this->validators as $validator) {
            $error = $validator->validate($value);
            if ($error !== null) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    public function processInput($input)
    {
        return $input === '' ? $this->defaultValue : $input;
    }
}