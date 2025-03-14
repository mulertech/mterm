<?php

namespace MulerTech\MTerm\Form;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\FieldInterface;

/**
 * Class Form
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class Form
{
    private FormRenderer $renderer;
    private array $fields = [];
    private array $values = [];
    private array $errors = [];
    private bool $isSubmitted = false;
    private bool $isValid = false;

    /**
     * @param Terminal $terminal
     */
    public function __construct(Terminal $terminal)
    {
        $this->renderer = new FormRenderer($terminal);
    }

    /**
     * Add a field to the form
     *
     * @param FieldInterface $field Field to add
     * @return self
     */
    public function addField(FieldInterface $field): self
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * Handle form submission
     *
     * @return bool True if the form is valid
     */
    public function handle(): bool
    {
        $this->isSubmitted = true;
        $this->values = [];
        $this->errors = [];

        foreach ($this->fields as $field) {
            $value = match (true) {
                $field->isMultipleInput() && $field->isMultipleSelection() => $this->renderer->renderSelectMultipleField($field),
                $field->isMultipleInput() && !$field->isMultipleSelection() => $this->renderer->renderSelectSingleField($field),
                default => $this->renderer->renderField($field),
            };
            $this->values[$field->getName()] = $value;

            $fieldErrors = $field->validate($value);
            if (!empty($fieldErrors)) {
                $this->errors[$field->getName()] = $fieldErrors;
            }
        }

        $this->isValid = empty($this->errors);

        if (!$this->isValid) {
            $this->renderer->renderErrors($this->errors);
        }

        return $this->isValid;
    }

    /**
     * Check if the form has been submitted
     *
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return $this->isSubmitted;
    }

    /**
     * Check if the form is valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Get all form values
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Get a specific form value
     *
     * @param string $fieldName Field name
     * @return mixed|null Field value or null if not found
     */
    public function getValue(string $fieldName): mixed
    {
        return $this->values[$fieldName] ?? null;
    }

    /**
     * Get all form errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}