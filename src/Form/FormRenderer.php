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
     * @return string|int|float|null
     */
    public function renderField(FieldInterface $field): string|int|float|null
    {
        $field->clearErrors();

        $prompt = $this->buildPrompt($field);

        if ($field->getDescription()) {
            $this->terminal->writeLine($field->getDescription(), 'cyan');
        }

        if ($field instanceof PasswordField && $field->isMaskInput()) {
            return $this->renderPasswordField($field, $prompt);
        }

        $value = $this->terminal->read($prompt);
        return $field->processInput($value);
    }

    /**
     * @param SelectField $field
     * @return array|null
     */
    public function renderSelectMultipleField(SelectField $field): ?array
    {
        $field->clearErrors();

        $result = $this->handleSelectField($field);

        if ($result === true && $field->getSelectedOptions() !== []) {
            return $field->getSelectedOptions();
        }

        return $field->getDefault() ?? [];
    }

    /**
     * @param SelectField $field
     * @return string|null
     */
    public function renderSelectSingleField(SelectField $field): ?string
    {
        $field->clearErrors();

        $result = $this->handleSelectField($field);

        if ($result === true) {
            return $field->getCurrentOption();
        }

        return $field->getDefault() ?? '';
    }

    /**
     * @param array $errors
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

    /**
     * @param SelectField $field
     * @return bool
     */
    private function handleSelectField(SelectField $field): bool
    {
        $prompt = $this->buildPrompt($field);
        $header = $prompt . PHP_EOL;
        if ($field->getDescription() !== null) {
            $header .= $field->getDescription() . PHP_EOL;
        }

        $this->terminal->specialMode();
        $this->terminal->write($header, 'cyan');
        $this->terminal->write($field->processInput(''));

        $result = $this->handleSelectKeyboardInput($field, $header);

        $this->terminal->normalMode();
        return $result;
    }

    /**
     * @param SelectField $field
     * @param string $header
     * @return bool
     */
    private function handleSelectKeyboardInput(SelectField $field, string $header): bool
    {
        while (true) {
            $char = $this->terminal->readChar();

            if ($char === PHP_EOL) { // Enter key
                return true;
            }

            if ($char === "\033") {
                $this->handleArrowKey($field, $header);
                continue;
            }

            if ($char === ' ') {
                $this->terminal->clear();
                $this->terminal->write($header, 'cyan');
                $this->terminal->write($field->processInput('space'));
            }

            if ($char === 'a') {
                $this->terminal->clear();
                $this->terminal->write($header, 'cyan');
                $this->terminal->write($field->processInput('a'));
            }
        }
    }

    /**
     * @param SelectField $field
     * @param string $header
     * @return void
     */
    private function handleArrowKey(SelectField $field, string $header): void
    {
        $sequence = $this->terminal->readChar() . $this->terminal->readChar();

        if ($sequence === "[A") { // Up arrow
            $this->terminal->clear();
            $this->terminal->write($header, 'cyan');
            $this->terminal->write($field->processInput('up'));
        } elseif ($sequence === "[B") { // Down arrow
            $this->terminal->clear();
            $this->terminal->write($header, 'cyan');
            $this->terminal->write($field->processInput('down'));
        }
    }

    /**
     * @param PasswordField $field
     * @param string $prompt
     * @return string|null
     */
    private function renderPasswordField(PasswordField $field, string $prompt): ?string
    {
        $this->terminal->write($prompt);
        $this->terminal->specialMode();

        $password = '';

        while (true) {
            $char = $this->terminal->readChar();

            // Enter key pressed
            if ($char === PHP_EOL) {
                $this->terminal->writeLine('');
                break;
            }

            // Backspace handling
            if ($char === "\x7F" || $char === "\x08") {
                if (strlen($password) > 0) {
                    $password = substr($password, 0, -1);
                    $this->terminal->write("\x08 \x08");
                }
            } // Regular character
            elseif (ord($char) >= 32) {
                $password .= $char;
                $this->terminal->write($field->getMaskChar());
            }
        }

        $this->terminal->normalMode();
        return $field->processInput($password);
    }
}
