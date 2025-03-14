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
     * Render a form field and get user input
     *
     * @param FieldInterface $field Field to render
     * @return string|null User input value
     */
    public function renderField(FieldInterface $field): ?string
    {
        $label = $field->getLabel();
        $required = $field->isRequired() ? ' (required)' : '';
        $prompt = "{$label}{$required}: ";

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
     * @return string|null
     */
    public function renderSelectMultipleField(SelectField $field): ?string
    {
        $label = $field->getLabel();
        $required = $field->isRequired() ? ' (required)' : '';
        $prompt = "{$label}{$required}: ";
        $header = $prompt . PHP_EOL . $field->getDescription() . PHP_EOL . PHP_EOL;

        $this->terminal->specialMode();

        $this->terminal->clear();
        $this->terminal->write($header, 'cyan');
        $this->terminal->write($field->processInput(''));

        do {
            $char = $this->terminal->readChar();

            if ($char === "\033") { // Escape sequence
                $char .= fgetc(STDIN); // [
                $char .= fgetc(STDIN); // A, B, C, or D

                if ($char === "\033[A") { // Arrow up
                    $this->terminal->clear();
                    $this->terminal->write($header, 'cyan');
                    $this->terminal->write($field->processInput('up'));
                } elseif ($char === "\033[B") { // Arrow down
                    $this->terminal->clear();
                    $this->terminal->write($header, 'cyan');
                    $this->terminal->write($field->processInput('down'));
                }
            } elseif ($char === ' ') { // Space to select/unselect
                echo 'yes space';
                $this->terminal->clear();
                $this->terminal->write($header, 'cyan');
                $this->terminal->write($field->processInput('space'));
            } elseif ($char === 'a') {
                $this->terminal->clear();
                $this->terminal->write($header, 'cyan');
                $this->terminal->write($field->processInput('space'));
            }
        } while ($char !== "\n" && $char !== 'q'); // Enter for validation, q to quit

        $this->terminal->normalMode();

        return $char === 'q' ? '' : $field->getPlainSelectedOptions();
    }

    /**
     * @param SelectField $field
     * @return string|null
     */
    public function renderSelectSingleField(SelectField $field): ?string
    {
        $label = $field->getLabel();
        $required = $field->isRequired() ? ' (required)' : '';
        $prompt = "{$label}{$required}: ";
        $header = $prompt . PHP_EOL . $field->getDescription() . PHP_EOL . PHP_EOL;

        $this->terminal->specialMode();

        $this->terminal->clear();
        $this->terminal->write($header, 'cyan');
        $this->terminal->write($field->processInput(''));

        do {
            $char = $this->terminal->readChar();

            if ($char === "\033") { // Escape sequence
                $char .= fgetc(STDIN); // [
                $char .= fgetc(STDIN); // A, B, C, or D

                if ($char === "\033[A") { // Arrow up
                    $this->terminal->clear();
                    $this->terminal->write($header, 'cyan');
                    $this->terminal->write($field->processInput('up'));
                } elseif ($char === "\033[B") { // Arrow down
                    $this->terminal->clear();
                    $this->terminal->write($header, 'cyan');
                    $this->terminal->write($field->processInput('down'));
                }
            }
        } while ($char !== "\n" && $char !== 'q'); // Enter for validation, q to quit

        $this->terminal->normalMode();

        return $char === 'q' ? '' : $field->getCurrentOption();
    }

    /**
     * Render a password field with masked input
     *
     * @param PasswordField $field
     * @param string $prompt
     * @return string|null
     */
    private function renderPasswordField(PasswordField $field, string $prompt): ?string
    {
        $this->terminal->write($prompt);

        // Enable special mode for character-by-character input
        $this->terminal->specialMode();

        $password = '';

        // Read characters until Enter is pressed
        while (true) {
            $char = $this->terminal->readChar();

            // Enter key was pressed, end input
            if ($char === "\n" || $char === "\r") {
                $this->terminal->writeLine('');
                break;
            }

            // Backspace or Delete key
            if ($char === "\x7F" || $char === "\x08") {
                if (strlen($password) > 0) {
                    $password = substr($password, 0, -1);
                    // Erase the last character from the screen
                    $this->terminal->write("\x08 \x08");
                }
            }
            // Regular character
            elseif (ord($char) >= 32) { // Printable characters
                $password .= $char;
                $this->terminal->write($field->getMaskChar());
            }
        }

        // Restore terminal to normal mode
        $this->terminal->normalMode();

        return $field->processInput($password);
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