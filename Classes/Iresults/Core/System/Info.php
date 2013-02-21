<?php
namespace Iresults\Core\System;

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
 * Security
 */
if (realpath($_SERVER["SCRIPT_FILENAME"]) === '' . realpath(__FILE__)) {
	echo "Die sucker!";
	die();
}



/**
 * The iresults backtrace enables you to display a backtrace.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_System
 */
class Info {
	/**
	 * The regular expression to match a class definition.
	 */
	const CLASS_DEFINITION_PATTERN = '/class[\s]+(%s)[\s]/i';

	/**
	 * Indicates if the current run is within the shutdown handler.
	 *
	 * @var boolean
	 */
	static protected $shutdownRun = FALSE;

	/**
	 * Displays the output of the builtin phpinfo() function.
	 *
	 * @return	void
	 */
	static public function info() {
		phpinfo();
	}

	/**
	 * Displays information how the script got to this point.
	 *
	 * @param	mixed	$here	 An optional parameter to debug
	 * @return	void
	 */
	static public function howDidIGetHere($here = NULL) {
		/*
		 * Register the shutdown handler.
		 */
		register_shutdown_function(array('\Iresults\Core\System\Info', 'shutdown'));

		/*
		 * Display basic PHP information.
		 */
		echo "<h1>Server Info</h1>";
		$info = "Server: " . $_SERVER["SERVER_SOFTWARE"] . "\n" .
		"PHP: " . PHP_VERSION . "\n" .
		$_SERVER["SERVER_NAME"] . " (" . $_SERVER["SERVER_ADDR"] . ":" . $_SERVER["SERVER_PORT"] . ")\n";
		if (zend_version()) {
			$info .= "Zend Engine v" . zend_version();
		}
		echo self::_wrapIntoPre($info);

		if (!self::$shutdownRun) {
			self::showCallStack();
		}
		self::showClassList();
		self::showFunctionList();
		//self::showVariableList();
		self::showFileList();

	}

	/**
	 * Displays the call stack.
	 *
	 * @return	void
	 */
	static public function showCallStack() {
		/*
		 * Display the call stack.
		 */
		echo '<div class="ir_debug_container" style="text-align:left;">';
		echo '<h1>Call stack</h1>';
		echo '<pre class="ir_debug">';
		if (class_exists("\Iresults\Core\System\Backtrace")) {
			$bt = new \Iresults\Core\System\Backtrace(2);
			echo $bt->render();
		} else {
			debug_print_backtrace();
		}
		echo '</pre></div>';
	}

	/**
	 * Displays a list of all available classes.
	 *
	 * @return	void
	 */
	static public function showClassList() {
		$list = get_declared_classes();
		$output = '<div class="ir_debug_container" style="text-align:left;">';
		$output .= "<h1>List of available classes</h1>";

		$classList = array();
		foreach ($list as $class) {
			$classListEntry = "$class";

			$classFile = self::getClassFileOfClass($class);
			if ($classFile) {
				$classListEntry .= " \t\t(<a href='file://$classFile'>$classFile</a>')";
			}
			$classList[] = $classListEntry;
		}

		$output .= self::createTableFromList($classList);

		$output .= '</div>';
		echo $output;
	}

	/**
	 * Displays a list of all available functions.
	 *
	 * @return	void
	 */
	static public function showFunctionList() {
		$functions = get_defined_functions();
		$output = '<div class="ir_debug_container" style="text-align:left;">';
		$output .= "<h1>List of available functions</h1>";

		$output .= "<h2>User functions</h2>";
		$output .= self::createTableFromList($functions["user"]);

		$output .= "<h2>Internal functions</h2>";
		$functionList = array();
		foreach ($functions["internal"] as $function) {
			$help = "http://li.php.net/manual/en/function." . str_replace("_","-",$function);
			$functionList[] = "<a href='$help'>$function</a>\n";
		}
		$output .= self::createTableFromList($functionList);



		$output .= '</div>';
		echo $output;
	}

	/**
	 * Displays a list of all variables in the current symbol table.
	 *
	 * @return	void
	 */
	static public function showVariableList() {
		$list = get_defined_vars();
		$output = '<div class="ir_debug_container" style="text-align:left;">';
		$output .= "<h1>List of all variables in the current symbol table</h1>";

		$varsList = array();
		foreach ($list as $key => $value) {
			$varsList[] = "$key => $value";
		}

		$output .= self::createTableFromList($varsList);
		$output .= self::_wrapIntoPre(implode("\n",$varsList));

		$output .= '</div>';
		echo $output;
	}

	/**
	 * Displays a list of all included files.
	 *
	 * @return	void
	 */
	static public function showFileList() {
		/*
		 * Display the file inclusion list.
		 */
		$bt = debug_backtrace();
		$i = 0;
		while (@$bt[$i]['function'] == 'howDidIGetHere' OR @$bt[$i]['function'] == 'call_user_func_array' OR
			@$bt[$i]['function'] == 'call_user_func') {
			$i++;
		}
		$i += 2;
		$callLine = $bt[$i]['line'];
		$fileAbs = $bt[$i]['file'];
		$fileRel = str_replace(dirname($_SERVER['SCRIPT_FILENAME']),'',$fileAbs);

		$inclusionList = get_included_files();
		$includedFile = current($inclusionList);
		$j = 1;
		echo '<div class="ir_debug_container" style="text-align:left;">';
		echo "<h1>List of included files</h1>";
		while ($includedFile != $fileAbs) {
			echo "#$j: $includedFile<br />";

			next($inclusionList);
			$includedFile = current($inclusionList);
			$j++;
		}

		if (!$fileAbs) {
			$fileAbs = $_SERVER['SCRIPT_FILENAME'];
		}
		if (!self::$shutdownRun) {
			echo "<span style='color:#f00'>You are here: $fileAbs @ $callLine</span>";
		}
		echo '</div>';
	}


	/**
	 * Returns the file the given class is defined in.
	 *
	 * @param	string	$class The name of the class
	 * @return	string    The file path or an empty string on error
	 */
	static public function getClassFileOfClass($class) {
		$withinANamespace = FALSE;
		/*
		 * Check if the class name contains information about a namespace
		 * (signaled through '\') and is not in the global space.
		 * If this is TRUE, check if the class name starts with one of the
		 * standard class strings.
		 */
		if (strpos($class,'\\')) {
			$withinANamespace = TRUE;
		} else if (substr($class,0,3) === 'Spl' ||
			substr($class,0,10) === 'Reflection' ||
			substr($class,0,5) === 'Array' ||
			substr($class,0,3) === 'DOM'
			) {
			return "";
		}

		$classFile = "";

		if (version_compare(PHP_VERSION, "5.0.0") >= 0) {
			$reflectionClass = new ReflectionClass($class);
			$classFile = $reflectionClass->getFileName();
		} else {
			$pattern = sprintf(self::CLASS_DEFINITION_PATTERN,$class);

			$inclusionList = get_included_files();
			foreach ($inclusionList as $file) {
				$handle = @fopen($file, "r");
				if ($handle) {
					while (($line = fgets($handle, 4096)) !== FALSE) {
						if (preg_match($pattern, $line)) {
							$classFile = $file;
							@fclose($handle);
							break;
						}
					}
					@fclose($handle);
				}
			}
		}
		return $classFile;
	}

	/**
	 * Create a HTML table with the contents from the given array.
	 *
	 * @param	array<string> $list An array of elements
	 * @return	string    The HTML code
	 */
	static public function createTableFromList(&$list) {
		$code = "";
		$entries = count($list);
		$maxColumns = 4;
		while ($maxColumns && ($entries / $maxColumns) < 1) {
			$maxColumns--;
		}
		if (!$maxColumns) {
			$maxColumns = 1;
		}

		$code .= "<table style='font-size:11px; width=100%;'>";
		$code .= "<tr>";

		$maxRows = ceil($entries / $maxColumns);
		$currentColumn = 0;
		for($i = 0; $i < $entries; $i++) {
			$element = $list[$i];
			if ($currentColumn >= $maxColumns) {
				$code .= "</tr><tr>";
				$currentColumn = 0;
			}

			$code .= "<td>$element</td>";
			$currentColumn++;
		}

		$code .= "</tr>";
		$code .= "</table>";
		return $code;
	}

	/**
	 * Wraps the given output in the ir_debug PRE-tag.
	 *
	 * @param	string	$output The output to wrap
	 * @return	string    The wrapped output
	 */
	static protected function _wrapIntoPre($output) {
		return '<pre class="ir_debug">' . $output . '</pre>';
	}

	/**
	 * The shutdown function.
	 *
	 * @return	void
	 */
	static public function shutdown() {
		self::$shutdownRun = TRUE;
		self::howDidIGetHere();
	}
}