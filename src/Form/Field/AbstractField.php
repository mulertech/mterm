<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Core\Terminal;
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
    /**
     * @var array<string>
     */
    protected array $errors = [];
    protected bool $required = false;
    /**
     * @var string|int|float|array<string>|null
     */
    protected string|int|float|array|null $defaultValue = null;
    /**
     * @var array<ValidatorInterface>
     */
    protected array $validators = [];
    protected bool $multipleInput = false;
    protected ?Terminal $terminal = null;

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
     * @return string|int|float|array<string>|null
     */
    public function getDefault(): string|int|float|array|null
    {
        return $this->defaultValue;
    }

    /**
     * @param string|int|float|array<string> $defaultValue
     * @return $this
     */
    public function setDefault(string|int|float|array $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @return void
     */
    public function clearErrors(): void
    {
        $this->errors = [];
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
     * @param string|int|float|array<int|string, string>|null $value
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        // Check required constraint
        if ($this->required && ($value === null || $value === '' || $value === [])) {
            $this->errors[] = "This field is required.";
            return $this->errors;
        }

        // Run all validators
        foreach ($this->validators as $validator) {
            $error = $validator->validate($value);
            if ($error !== null) {
                $this->errors[] = $error;
            }
        }

        return $this->errors;
    }

    /**
     * @param string $input
     * @return string|int|float|array<int|string, string>
     */
    public function processInput(string $input): string|int|float|array
    {
        if ($input !== '') {
            return $input;
        }

        if ($this->defaultValue !== null && !is_array($this->defaultValue)) {
            return $this->defaultValue;
        }

        return '';
    }

    /**
     * Set terminal instance
     *
     * @param Terminal $terminal
     * @return $this
     */
    public function setTerminal(Terminal $terminal): self
    {
        $this->terminal = $terminal;
        return $this;
    }
}
