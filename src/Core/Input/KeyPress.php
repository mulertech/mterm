<?php

namespace MulerTech\MTerm\Core\Input;

/**
 * One press: either a known key, or the character it produced.
 *
 * @author Sébastien Muler
 */
final readonly class KeyPress
{
    private function __construct(
        public ?Key $key,
        public string $character,
    ) {
    }

    public static function ofKey(Key $key, string $character = ''): self
    {
        return new self($key, $character);
    }

    public static function ofCharacter(string $character): self
    {
        return new self(null, $character);
    }

    /**
     * The stream has nothing left to give: no key, no character.
     */
    public static function endOfInput(): self
    {
        return new self(null, '');
    }

    public function is(Key $key): bool
    {
        return $key === $this->key;
    }

    /**
     * Whether the press produced text, as opposed to a bare control key.
     */
    public function isCharacter(): bool
    {
        return '' !== $this->character;
    }

    public function isEndOfInput(): bool
    {
        return null === $this->key && '' === $this->character;
    }
}
