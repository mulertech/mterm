<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Core\Terminal;

/**
 * Class SelectField.
 *
 * @author Sébastien Muler
 */
class SelectField extends AbstractField
{
    /**
     * @var array<int|string, string>
     */
    protected array $options = [];
    protected bool $multipleSelection = false;
    protected string $checkboxUnchecked = '[ ]';
    protected string $checkboxChecked = '[X]';
    protected string $cursorAbsent = ' ';
    protected string $cursorPresent = '*';
    private int $cursorPosition = 0;
    /**
     * @var array<int|string, string>
     */
    protected array $selectedOptions = [];

    public function __construct(string $name, string $label, bool $multipleSelection = false)
    {
        parent::__construct($name, $label);
        $this->multipleInput = true;
        $this->multipleSelection = $multipleSelection;
    }

    /**
     * @param array<int|string, string> $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setDefault(float|array|int|string $defaultValue): AbstractField
    {
        if ([] === $this->options) {
            throw new \RuntimeException('Options must be set before setting a default value');
        }
        if (is_numeric($defaultValue)) {
            throw new \RuntimeException('Default value must be a string or an array of strings');
        }
        $defaultValueArray = is_array($defaultValue) ? $defaultValue : [$defaultValue];
        if ([] !== array_diff($defaultValueArray, array_keys($this->options))) {
            throw new \RuntimeException('Default value must be one or multiple of the options');
        }

        return parent::setDefault($defaultValueArray);
    }

    /**
     * @return $this
     */
    public function setMultipleSelection(bool $multipleSelection = true): self
    {
        $this->multipleSelection = $multipleSelection;

        return $this;
    }

    public function isMultipleSelection(): bool
    {
        return $this->multipleSelection;
    }

    public function parseInput(string $input): string
    {
        // Generate the selected options
        if ($this->multipleSelection) {
            // Update the position of the cursor
            if ('up' === $input) {
                $this->cursorPosition = max(0, $this->cursorPosition - 1);
            } elseif ('down' === $input) {
                $this->cursorPosition = min(count($this->options) - 1, $this->cursorPosition + 1);
            } elseif ('space' === $input) {
                $key = array_keys($this->options)[$this->cursorPosition];
                if (isset($this->selectedOptions[$key])) {
                    unset($this->selectedOptions[$key]);
                } else {
                    $this->selectedOptions[$key] = $this->options[$key];
                }
            } elseif ('a' === $input) {
                $this->selectedOptions = $this->options;
            }

            return $this->processSelect(true);
        }

        // Update the position of the cursor
        if ('up' === $input) {
            $this->cursorPosition = max(0, $this->cursorPosition - 1);
        } elseif ('down' === $input) {
            $this->cursorPosition = min(count($this->options) - 1, $this->cursorPosition + 1);
        }

        return $this->processSelect();
    }

    /**
     * Process user input for this field.
     *
     * @return string|array<int|string, string>
     */
    public function processInput(string $input = ''): string|array
    {
        $terminal = $this->terminal;

        if (null === $terminal) {
            throw new \RuntimeException('Terminal must be set before calling processInput');
        }

        $this->clearErrors();

        if ($this->isMultipleSelection()) {
            return $this->renderSelectMultipleField($terminal);
        }

        return $this->renderSelectSingleField($terminal);
    }

    /**
     * @return array<int|string, string>
     */
    public function renderSelectMultipleField(Terminal $terminal): array
    {
        $this->clearErrors();

        $result = $this->handleSelectField($terminal);

        if (true === $result && [] !== $this->selectedOptions) {
            return $this->selectedOptions;
        }

        assert(is_array($this->getDefault()));

        return !empty($this->getDefault()) ? array_intersect_key($this->options, array_flip($this->getDefault())) : [];
    }

    public function renderSelectSingleField(Terminal $terminal): string
    {
        $this->clearErrors();

        $result = $this->handleSelectField($terminal);

        $defaultValue = $this->getDefault() ?? '';
        $defaultValue = is_string($defaultValue) ? $defaultValue : '';

        return true === $result ? $this->getCurrentOption() : $defaultValue;
    }

    private function handleSelectField(Terminal $terminal): bool
    {
        $prompt = $this->buildPrompt();
        $header = $prompt.PHP_EOL;

        if (null !== $this->getDescription()) {
            $header .= $this->getDescription().PHP_EOL;
        }

        $terminal->specialMode();
        $terminal->write($header, 'cyan');
        $terminal->write($this->parseInput(''));

        $result = $this->handleSelectKeyboardInput($header, $terminal);

        $terminal->normalMode();

        return $result;
    }

    private function handleSelectKeyboardInput(string $header, Terminal $terminal): bool
    {
        while (true) {
            $char = $terminal->readChar();

            if (PHP_EOL === $char) { // Enter key
                return true;
            }

            if ("\033" === $char) {
                $this->handleArrowKey($header, $terminal);
                continue;
            }

            if (' ' === $char) {
                $terminal->clear();
                $terminal->write($header, 'cyan');
                $terminal->write($this->parseInput('space'));
            }

            if ('a' === $char) {
                $terminal->clear();
                $terminal->write($header, 'cyan');
                $terminal->write($this->parseInput('a'));
            }
        }
    }

    private function handleArrowKey(string $header, Terminal $terminal): void
    {
        $sequence = $terminal->readChar().$terminal->readChar();

        $terminal->clear();
        $terminal->write($header, 'cyan');

        $terminal->write($this->parseInput('[A' === $sequence ? 'up' : 'down'));
    }

    /**
     * @param string|int|float|array<int|string, string>|null $value
     *
     * @return array<string>
     */
    public function validate(string|int|float|array|null $value): array
    {
        $this->errors = parent::validate($value);

        if (empty($value) || is_numeric($value)) {
            return $this->errors;
        }

        if ($this->multipleSelection && is_array($value)) {
            foreach ($value as $key => $val) {
                if (!array_key_exists($key, $this->options)) {
                    $this->errors[] = "Invalid option: '$key'.";
                }
            }

            return $this->errors;
        }

        if (!is_array($value) && !array_key_exists($value, $this->options)) {
            $this->errors[] = "Invalid option: '$value'.";
        }

        return $this->errors;
    }

    /**
     * Get the current selected option key.
     */
    public function getCurrentOption(): string
    {
        $options = array_keys($this->options);

        return (string) ($options[$this->cursorPosition] ?? '');
    }

    private function buildPrompt(): string
    {
        $label = $this->getLabel();
        $required = $this->isRequired() ? ' (required)' : '';

        return "$label$required: ";
    }

    /**
     * Generate the select display.
     */
    private function processSelect(bool $multiple = false): string
    {
        $echo = '';
        $selected = '';
        $position = 0;

        foreach ($this->options as $key => $value) {
            if ($multiple) {
                $selected = isset($this->selectedOptions[$key]) ? $this->checkboxChecked : $this->checkboxUnchecked;
                $selected .= ' ';
            }
            $cursor = ($position++ === $this->cursorPosition) ? $this->cursorPresent : $this->cursorAbsent;
            $echo .= "$cursor $selected $value".PHP_EOL;
        }

        return $echo;
    }
}
