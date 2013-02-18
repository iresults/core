<?php
namespace Iresults\Core\System;

/*
 * Copyright notice
 *
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *             Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */



/**
 * The iresults exception handler displays a backtrace in case of an exception.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_System
 */
class ExceptionHandler {
	/**
	 * Handle the given exception.
	 *
	 * @param	Exception	$exception
	 * @return	void
	 */
	static public function handleException(Exception $exception = NULL) {
		$bt = new \Iresults\Core\System\Backtrace(2);
		if ($exception) {
			echo "Exception thrown ".$exception->getMessage()." (".$exception->getCode().")<br>";
		} else {
			echo "Unnamed exception<br>";
		}

		echo "
			<pre>
			".$bt->render()."
			</pre>
		";

	}
}