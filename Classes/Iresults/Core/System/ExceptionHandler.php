<?php

namespace Iresults\Core\System;

use Exception;

/**
 * The iresults exception handler displays a backtrace in case of an exception.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_System
 */
class ExceptionHandler
{
    /**
     * Handle the given exception.
     *
     * @param Exception $exception
     * @return    void
     */
    static public function handleException(Exception $exception = null)
    {
        $bt = new Backtrace(2);
        if ($exception) {
            echo "Exception thrown " . $exception->getMessage() . " (" . $exception->getCode() . ")<br>";
        } else {
            echo "Unnamed exception<br>";
        }

        echo "
			<pre>
			" . $bt->render() . "
			</pre>
		";
    }
}
