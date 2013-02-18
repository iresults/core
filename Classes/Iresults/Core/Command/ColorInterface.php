<?php
namespace Iresults\Core\Command;

/***************************************************************
*  Copyright notice
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

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
?>
