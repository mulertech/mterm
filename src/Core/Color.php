<?php

namespace MulerTech\MTerm\Core;

/**
 * The eight foreground colors every ANSI terminal renders.
 *
 * @author Sébastien Muler
 */
enum Color: string
{
    case Black = '30';
    case Red = '31';
    case Green = '32';
    case Yellow = '33';
    case Blue = '34';
    case Magenta = '35';
    case Cyan = '36';
    case White = '37';

    /**
     * Sequence restoring the default foreground and weight.
     */
    public const string RESET = "\033[0m";

    /**
     * Sequence opening a run of text in this color.
     */
    public function sequence(bool $bold = false): string
    {
        return "\033[".($bold ? '1' : '0').';'.$this->value.'m';
    }
}
