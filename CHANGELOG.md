# Release notes for mterm

## v2.0.3 - 2026-09-15

- Fixed: Ctrl+C while a program waited for a key — "Press any key to return.", a menu — did nothing until the next key was pressed. PHP retries a read a signal interrupts, so the handler that restores the terminal only ran once the read returned. Waiting for input now goes through `stream_select`, which a signal interrupts without retry, and the handler runs at once. Streams that cannot be waited upon keep reading directly.
- Fixed: leaving on a signal restored the terminal mode but not the cursor a menu hides, and left the shell prompt on the same line as the last message. The cursor is shown again, on a line of its own.
- Changed: the interruption handlers no longer restart the system call a signal interrupts.

## v2.0.2 - 2026-09-15

- Fixed: a key pressed while a menu action ran — Enter struck after a confirmation, a key pressed to check the program was still alive — stayed in the terminal and answered "Press any key to return." at once, so the menu came back before the action's report could be read. The menu now discards the input typed ahead before that prompt. Only an interactive terminal that can be waited upon is drained: input from a file or a pipe is kept whole, and a Windows console is left as it is.
- Added: `Terminal::discardPendingInput()` and `InputReader::discardPending()`.

## v2.0.1 - 2026-09-15

- Fixed: `Terminal::clear()` also erases the scrollback (`\033[3J`). Some terminals — PhpStorm's among them — answered the erasing of the screen by pushing it into the scrollback, so the last line of what came before stayed visible above each menu. The menu, which clears before every drawing and every action, now shows alone in every terminal.

## v2.0.0 - 2026-09-02

- Changed: `Core` is rewritten and its API breaks — hence the major version. `Form`, `Command` and `Utils` keep their behaviour and are only reconnected to the new output.
- Added: an abstract output — `Core\Output\OutputInterface`, `StreamOutput`, `BufferedOutput`. Display is no longer an `echo`, so it can be captured, redirected and tested.
- Added: `Core\Color`, an enum replacing the string constants, and decoration detection that honours `NO_COLOR` and a non-terminal output. A redirected output no longer carries escape sequences.
- Changed: `clear()`, `clearLine()`, `moveCursor()`, `hideCursor()` and `showCursor()` write ANSI sequences instead of forking a process. `system('clear')` is gone. The only remaining `system()` is `CommandRunner::runDirect()`, outside the display path, where a command must write to the terminal in real time.
- Added: `Core\TerminalMode`, which saves the terminal state with `stty -g` before changing it and restores it through a shutdown handler and signal handlers. It refuses to enter raw mode when it cannot read the state, rather than entering a mode it could not undo.
- Added: `Core\Input\InputReader`, `Key` and `KeyPress`. Arrows, Escape, Delete, Home/End, PageUp/PageDown and F1–F12 are recognised (CSI and SS3), and multi-byte UTF-8 characters are returned whole — where the previous `readChar()` read a single byte and could read neither.
- Added: `Ui\Indicator`, `IndicatorStatus` and `IndicatorRenderer` — four states with one shape and one colour each. A failing indicator built without the label of its remedy throws: the rule holds because it cannot be bypassed.
- Added: `Ui\Menu` and `Ui\MenuItem` — nesting, current path, back and quit. The menu carries the cycle around an action: confirm, run, catch, return. An item declares its confirmation instead of implementing it, so a destructive action cannot silently ship without one.
- Fixed: `SelectField` looped forever on an exhausted input stream, and never returned its default in single selection.
- Fixed: `CommandRunner` dereferenced pipes that did not exist when `proc_open` failed. It now fails immediately, naming the command.
- Note: `ext-pcntl` stays a suggestion — requiring it would reach, through `mulertech/database`, the images that do not install it. Without it, an interruption leaves the terminal without echo; that degradation is now announced on standard error along with its remedy (`stty sane`). A consumer driving an interactive session should require the extension itself.

## v1.0.2 - 2025-03-26

v1.0.2

## v1.0.1 - 2025-03-24

v1.0.1

## v1.0.0 - 2025-03-24

v1.0.0
