<?php

namespace MulerTech\MTerm\Form;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Form\Field\FieldInterface;
use MulerTech\MTerm\Form\Field\PasswordField;
use MulerTech\MTerm\Form\Field\SelectField;

/**
 * Class FormRenderer
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class FormRenderer
{
    private Terminal $terminal;

    /**
     * @param Terminal $terminal
     */
    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * @param FieldInterface $field
     * @return string|array<int|string, string>
     */
    public function renderField(FieldInterface $field): string|array
    {
        $field->clearErrors();
        $field->setTerminal($this->terminal);

        $prompt = $this->buildPrompt($field);

        if ($field instanceof PasswordField && $field->isMaskInput()) {
            return $field->processInput();
        }

        if ($field instanceof SelectField) {
            $result = $field->processInput();
            return is_array($result) ? $result : (string)$result;
        }

        if ($field->getDescription()) {
            $this->terminal->writeLine($field->getDescription(), 'cyan');
        }

        $value = $this->terminal->read($prompt);
        $result = $field->processInput($value);

        return is_array($result) ? $result : (string)$result;
    }

    /**
     * @param array<string> $errors
     * @return void
     */
    public function renderErrors(array $errors): void
    {
        $this->terminal->writeLine('Please correct the following errors:', 'red');

        foreach ($errors as $error) {
            $this->terminal->writeLine(" - $error", 'red');
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->terminal->clear();
    }

    /**
     * @param FieldInterface $field
     * @return string
     */
    private function buildPrompt(FieldInterface $field): string
    {
        $label = $field->getLabel();
        $required = $field->isRequired() ? ' (required)' : '';
        return "$label$required: ";
    }
}
