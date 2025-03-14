<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Form\Validator\ValidatorInterface;

/**
 * Class AbstractField
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
abstract class AbstractField implements FieldInterface
{
    protected string $name;
    protected string $label;
    protected ?string $description = null;
    protected bool $required = false;
    protected ?string $defaultValue = null;
    protected array $validators = [];
    protected bool $multipleInput = false;
    protected bool $multipleSelection = false;

    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function setRequired(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->defaultValue;
    }

    /**
     * @param string $defaultValue
     * @return $this
     */
    public function setDefault(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param bool $multipleInput
     * @return $this
     */
    protected function setMultipleInput(bool $multipleInput): self
    {
        $this->multipleInput = $multipleInput;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultipleInput(): bool
    {
        return $this->multipleInput;
    }

    /**
     * @return bool
     */
    public function isMultipleSelection(): bool
    {
        return $this->multipleSelection;
    }

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function addValidator(ValidatorInterface $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * @param string|null $value
     * @return array
     */
    public function validate(?string $value): array
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

    /**
     * @param string $input
     * @return string|int|null|float
     */
    public function processInput(string $input): string|int|null|float
    {
        return $input === '' ? $this->defaultValue : $input;
    }
}