<?php

namespace MulerTech\MTerm\Form;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\FieldInterface;
use MulerTech\MTerm\Form\Field\SelectField;

/**
 * Class Form
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class Form
{
    private FormRenderer $renderer;
    /**
     * @var array<string, FieldInterface>
     */
    private array $fields = [];
    /**
     * @var array<string, string|int|float|array<int|string, string>|null>
     */
    private array $values = [];
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
     * @return void
     */
    public function handle(): void
    {
        $this->isSubmitted = true;
        $this->values = [];
        $this->renderer->clear();

        foreach ($this->fields as $field) {
            $this->handleField($field);
        }

        $this->isValid = true;
    }

    private function handleField(FieldInterface $field, bool $error = false): void
    {
        if (!$error) {
            $this->renderer->clear();
        }

        $value = $this->renderer->renderField($field);
        $this->values[$field->getName()] = $value;

        $fieldErrors = $field->validate($value);
        if (!empty($fieldErrors)) {
            $this->renderer->clear();
            $this->renderer->renderErrors($fieldErrors);
            $this->handleField($field, true);
        }
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
     * @return array<string, string|int|float|array<int|string, string>|null>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Get a specific form value
     *
     * @param string $fieldName Field name
     * @return string|int|float|array<int|string, string>|null Field value or null if not found
     */
    public function getValue(string $fieldName): string|int|float|array|null
    {
        return $this->values[$fieldName] ?? null;
    }
}
