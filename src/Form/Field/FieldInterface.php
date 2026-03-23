<?php

namespace MulerTech\MTerm\Form\Field;

use MulerTech\MTerm\Core\Terminal;

/**
 * Interface FieldInterface.
 *
 * @author Sébastien Muler
 */
interface FieldInterface
{
    /**
     * Get field name.
     */
    public function getName(): string;

    /**
     * Get field label.
     */
    public function getLabel(): string;

    /**
     * Get field description.
     */
    public function getDescription(): ?string;

    public function setDescription(string $description): self;

    /**
     * Check if field is required.
     */
    public function isRequired(): bool;

    public function setRequired(bool $required = true): self;

    public function clearErrors(): void;

    /**
     * @param string|int|float|array<string> $defaultValue
     */
    public function setDefault(string|int|float|array $defaultValue): self;

    /**
     * @return string|int|float|array<string>|null
     */
    public function getDefault(): string|int|float|array|null;

    /**
     * Process user input.
     *
     * @param string $input Raw input from user
     *
     * @return string|int|float|array<int|string, string> Processed input value
     */
    public function processInput(string $input): string|int|float|array;

    /**
     * Validate field value.
     *
     * @param string|int|float|array<int|string, string>|null $value Field value to validate
     *
     * @return array<string> List of error messages (empty if valid)
     */
    public function validate(string|int|float|array|null $value): array;

    /**
     * Set terminal instance to be used for input/output operations.
     */
    public function setTerminal(Terminal $terminal): self;
}
