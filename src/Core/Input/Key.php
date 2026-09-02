<?php

namespace MulerTech\MTerm\Core\Input;

/**
 * A key that carries no printable character of its own.
 *
 * @author Sébastien Muler
 */
enum Key
{
    case Up;
    case Down;
    case Right;
    case Left;
    case Enter;
    case Escape;
    case Backspace;
    case Delete;
    case Insert;
    case Tab;
    case Space;
    case Home;
    case End;
    case PageUp;
    case PageDown;
    case F1;
    case F2;
    case F3;
    case F4;
    case F5;
    case F6;
    case F7;
    case F8;
    case F9;
    case F10;
    case F11;
    case F12;
}
