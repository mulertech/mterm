<?php

namespace MulerTech\MTerm\Form\Field;

interface FieldInterface
{
    /**
     * Get field name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get field description
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Check if field is required
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * Process user input
     *
     * @param mixed $input Raw input from user
     * @return mixed Processed input value
     */
    public function processInput($input);

    /**
     * Validate field value
     *
     * @param mixed $value Field value to validate
     * @return array List of error messages (empty if valid)
     */
    public function validate($value): array;
}