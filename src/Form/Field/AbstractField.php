<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Validator\ValidatorInterface;

/**
 * Class AbstractField.
 *
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

    /**
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
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
     *
     * @return $this
     */
    public function setDefault(string|int|float|array $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * @return $this
     */
    public function addValidator(ValidatorInterface $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * @param string|int|float|array<int|string, string>|null $value
     *
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        // Check required constraint
        if ($this->required && (null === $value || '' === $value || [] === $value)) {
            $this->errors[] = 'This field is required.';

            return $this->errors;
        }

        // Run all validators
        foreach ($this->validators as $validator) {
            $error = $validator->validate($value);
            if (null !== $error) {
                $this->errors[] = $error;
            }
        }

        return $this->errors;
    }

    /**
     * @return string|int|float|array<int|string, string>
     */
    public function processInput(string $input): string|int|float|array
    {
        if ('' !== $input) {
            return $input;
        }

        if (null !== $this->defaultValue && !is_array($this->defaultValue)) {
            return $this->defaultValue;
        }

        return '';
    }

    /**
     * Set terminal instance.
     *
     * @return $this
     */
    public function setTerminal(Terminal $terminal): self
    {
        $this->terminal = $terminal;

        return $this;
    }
}
