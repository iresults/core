<?php
namespace Iresults\Core\Command;

/*
 * The MIT License (MIT)
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * A interface holding constants for use with Iresults::say in CLI
 * environments.
 *
 * Find more information about ANSI Escape codes on http://ascii-table.com/ansi-escape-sequences.php
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
interface ColorInterface {
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* ESCAPE CHARACTER  MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * The escape character
	 */
	const ESCAPE = "\033";

	/**
	 * The escape character
	 */
	const SIGNAL = self::ESCAPE;


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* COLORS            MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Bold color red
	 */
	const BOLD_RED = "[1;31m";

	/**
	 * Bold color green
	 */
	const BOLD_GREEN = "[1;32m";

	/**
	 * Bold with color blue
	 */
	const BOLD_BLUE = "[1;34m";

	/**
	 * Bold color cyan
	 */
	const BOLD_CYAN = "[1;36m";

	/**
	 * Bold color yellow
	 */
	const BOLD_YELLOW = "[1;33m";

	/**
	 * Bold color magenta
	 */
	const BOLD_MAGENTA = "[1;35m";

	/**
	 * Bold color white
	 */
	const BOLD_WHITE = "[1;37m";

	/**
	 * Normal
	 */
	const NORMAL = "[0m";

	/**
	 * Color black
	 */
	const BLACK = "[0;30m";

	/**
	 * Color red
	 */
	const RED = "[0;31m";

	/**
	 * Color green
	 */
	const GREEN = "[0;32m";

	/**
	 * Color yellow
	 */
	const YELLOW = "[0;33m";

	/**
	 * Color blue
	 */
	const BLUE = "[0;34m";

	/**
	 * Color cyan
	 */
	const CYAN = "[0;36m";

	/**
	 * Color magenta
	 */
	const MAGENTA = "[0;35m";

	/**
	 * Color brown
	 */
	const BROWN = "[0;33m";

	/**
	 * Color gray
	 */
	const GRAY = "[0;37m";

	/**
	 * Bold
	 */
	const BOLD = "[1m";


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* UNDERSCORE        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Underscored
	 */
	const UNDERSCORE = "[4m";


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* REVERSE           MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Reversed
	 */
	const REVERSE = "[7m";


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* CURSOR AND DISPLAY CONTROLS MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Turn all attributes off
	 */
	const ATTRIBUTES_OFF = self::NORMAL;

	/**
	 * Clear entire line
	 */
	const CLEAR_LINE = "[2K";

	/**
	 * Clear line from cursor right
	 */
	const CLEAR_LINE_TO_RIGHT = "[0K";

	/**
	 * Clear line from cursor right
	 */
	const CLEAR_LINE_TO_LEFT = "[1K";

	/**
	 * Clear entire screen
	 */
	const CLEAR_DISPLAY = "[2J";

	/**
	 * Clear screen from cursor down
	 */
	const CLEAR_DISPLAY_DOWN = "[0J";

	/**
	 * Clear screen from cursor up
	 */
	const CLEAR_DISPLAY_UP = "[1J";

	/**
	 * Moves cursor one line above
	 */
	const UP = "[A";

	/**
	 * Moves cursor one line under
	 */
	const DOWN = "[B";

	/**
	 * Moves cursor one spacing to the left
	 */
	const LEFT = "[C";

	/**
	 * Moves cursor one spacing to the right
	 */
	const RIGHT = "[D";

	/**
	 * Moves cursor to the upper-left corner of the screen (line 0, column 0)
	 */
	const HOME = "[0;0H";

	/**
	 * Beep sound
	 */
	const BEEP_SOUND = "\007";

	/**
	 * Save the cursor position
	 */
	const SAVE_CURSOR_POSITION = "7";

	/**
	 * Restore the cursor position
	 */
	const RESTORE_CURSOR_POSITION = "8";



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MACROS            MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Send a sequence to turn attributes off
	 */
	const SIGNAL_ATTRIBUTES_OFF = "\033[0m";
}
