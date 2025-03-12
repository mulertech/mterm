<?php

namespace MulerTech\MTerm\Form;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\FieldInterface;

class FormRenderer
{
    private Terminal $terminal;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * Render a form field and get user input
     *
     * @param FieldInterface $field Field to render
     * @return mixed User input value
     */
    public function renderField(FieldInterface $field)
    {
        $label = $field->getLabel();
        $required = $field->isRequired() ? ' (required)' : '';
        $prompt = "{$label}{$required}: ";

        if ($field->getDescription()) {
            $this->terminal->writeLine($field->getDescription(), 'cyan');
        }

        $value = $this->terminal->read($prompt);
        return $field->processInput($value);
    }

    /**
     * Render form validation errors
     *
     * @param array $errors Form errors
     * @return void
     */
    public function renderErrors(array $errors): void
    {
        $this->terminal->writeLine('Please correct the following errors:', 'red');

        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $this->terminal->writeLine(" - {$field}: {$error}", 'red');
            }
        }
    }
}