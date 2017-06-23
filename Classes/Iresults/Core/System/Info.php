<?php

namespace Iresults\Core\System;


/**
 * Security
 */
if (realpath($_SERVER["SCRIPT_FILENAME"]) === '' . realpath(__FILE__)) {
    echo "Die sucker!";
    die();
}

use Iresults\Core\Iresults;

/**
 * The iresults backtrace enables you to display a backtrace.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_System
 */
class Info
{
    /**
     * The regular expression to match a class definition.
     */
    const CLASS_DEFINITION_PATTERN = '/class[\s]+(%s)[\s]/i';

    /**
     * Indicates if the current run is within the shutdown handler.
     *
     * @var boolean
     */
    static protected $shutdownRun = false;

    /**
     * @var array
     */
    static protected $informationToList = [];

    /**
     * Displays the output of the builtin phpinfo() function.
     *
     * @return    void
     */
    static public function info()
    {
        phpinfo();
    }

    /**
     * Registers the shutdown() method to be called
     *
     * @param array $informationToList list of informations to display at the end
     */
    static public function registerShutdownFunction($informationToList = null)
    {
        if ($informationToList === null) {
            $informationToList = [
                'callStack',
                'classList',
                'functionList',
                'variableList',
                'fileList',
                'memoryUsage',
            ];
        }
        self::$informationToList = $informationToList;

        /*
        * Register the shutdown handler.
        */
        register_shutdown_function([__CLASS__, 'shutdown']);
    }

    /**
     * Displays information how the script got to this point.
     *
     * @return    void
     */
    static public function howDidIGetHere()
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;

        /*
         * Display basic PHP information.
         */
        self::_printHeadline('Server Info');

        $info = "Server: " . $_SERVER["SERVER_SOFTWARE"] . PHP_EOL
            . "PHP: " . PHP_VERSION . PHP_EOL;

        if (!$isShell) {
            $info .= $_SERVER["SERVER_NAME"] . ' (' . $_SERVER["SERVER_ADDR"] . ':' . $_SERVER["SERVER_PORT"] . ')' . PHP_EOL;
        }
        if (zend_version()) {
            $info .= 'Zend Engine v' . zend_version();
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
     * @return    void
     */
    static public function showCallStack()
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;

        /*
         * Display the call stack.
         */
        if (!$isShell) {
            echo '<div class="ir_debug_container" style="text-align:left;">';
        }

        self::_printHeadline('Call stack');
        if (!$isShell) {
            echo '<pre class="ir_debug">';
        }
        if (class_exists('\\Iresults\\Core\\System\\Backtrace')) {
            $bt = new \Iresults\Core\System\Backtrace(2);
            echo $bt->render();
        } else {
            debug_print_backtrace();
        }
        if (!$isShell) {
            echo '</pre></div>';
        }
    }

    /**
     * Displays a list of all available classes.
     *
     * @return    void
     */
    static public function showClassList()
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;

        $list = get_declared_classes();
        $output = !$isShell ? '<div class="ir_debug_container" style="text-align:left;">' : '';
        self::_printHeadline('List of available classes');

        $classList = [];
        foreach ($list as $class) {
            $classListEntry = "$class";

            $classFile = self::getClassFileOfClass($class);
            if ($classFile) {
                if ($isShell) {
                    $classListEntry .= " \t\t" . $classFile;
                } else {
                    $classListEntry .= " \t\t(<a href='file://$classFile'>$classFile</a>')";
                }

            }
            $classList[] = $classListEntry;
        }

        $output .= self::createTableFromList($classList);

        $output .= !$isShell ? '</div>' : PHP_EOL;
        echo $output;
    }

    /**
     * Displays a list of all available functions.
     *
     * @return    void
     */
    static public function showFunctionList()
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;

        $functions = get_defined_functions();
        $output = !$isShell ? '<div class="ir_debug_container" style="text-align:left;">' : '';
        self::_printHeadline('List of available functions');

        self::_printHeadline('User functions', 2);

        $output .= self::createTableFromList($functions["user"]);

        self::_printHeadline('Internal functions', 2);

        $functionList = [];
        foreach ($functions["internal"] as $function) {
            if ($isShell) {
                $functionList[] = $function;
            } else {
                $help = "http://li.php.net/manual/en/function." . str_replace("_", "-", $function);
                $functionList[] = "<a href='$help'>$function</a>\n";
            }
        }
        $output .= self::createTableFromList($functionList);


        $output .= !$isShell ? '</div>' : PHP_EOL;
        echo $output;
    }

    /**
     * Displays a list of all variables in the current symbol table.
     *
     * @return    void
     */
    static public function showVariableList()
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;

        $list = get_defined_vars();
        $output = !$isShell ? '<div class="ir_debug_container" style="text-align:left;">' : '';
        self::_printHeadline('List of all variables in the current symbol table');

        $varsList = [];
        foreach ($list as $key => $value) {
            $varsList[] = "$key => $value";
        }

        $output .= self::createTableFromList($varsList);
        $output .= self::_wrapIntoPre(implode("\n", $varsList));

        $output .= !$isShell ? '</div>' : PHP_EOL;
        echo $output;
    }

    /**
     * Displays a list of all included files.
     *
     * @return    void
     */
    static public function showFileList()
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;

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

        $inclusionList = get_included_files();
        $includedFile = current($inclusionList);
        $j = 1;

        if (!$isShell) {
            echo '<div class="ir_debug_container" style="text-align:left;">';
        }

        self::_printHeadline('List of included files');
        while ($includedFile != $fileAbs) {
            echo "#$j: $includedFile";
            if (!$isShell) {
                echo '<br />';
            }
            echo PHP_EOL;

            next($inclusionList);
            $includedFile = current($inclusionList);
            $j++;
        }

        if (!$fileAbs) {
            $fileAbs = $_SERVER['SCRIPT_FILENAME'];
        }
        if (!self::$shutdownRun) {
            if (!$isShell) {
                echo "<span style='color:#f00'>";
            }
            Iresults::say("You are here: $fileAbs @ $callLine", \Iresults\Core\Cli\ColorInterface::RED);
            if (!$isShell) {
                echo "</span>";
            }
        }
        if (!$isShell) {
            echo '</div>';
        }
    }


    /**
     * Displays information about the memory usage
     *
     * @return void
     */
    static public function showMemoryUsage()
    {
        self::_printHeadline('Memory');
        Iresults::say('Currently allocated: ' . self::_formatMemory(memory_get_usage(true)));
        Iresults::say('Peak allocated:      ' . self::_formatMemory(memory_get_peak_usage(true)));
        Iresults::say(PHP_EOL);
    }


    /**
     * Returns the file the given class is defined in.
     *
     * @param string $class The name of the class
     * @return    string    The file path or an empty string on error
     */
    static public function getClassFileOfClass($class)
    {
        /*
         * Check if the class name contains information about a namespace
         * (signaled through '\') and is not in the global space.
         * If this is TRUE, check if the class name starts with one of the
         * standard class strings.
         */
        if (strpos($class, '\\')) {
        } elseif (substr($class, 0, 3) === 'Spl' ||
            substr($class, 0, 10) === 'Reflection' ||
            substr($class, 0, 5) === 'Array' ||
            substr($class, 0, 3) === 'DOM'
        ) {
            return "";
        }

        $classFile = "";

        if (version_compare(PHP_VERSION, "5.0.0") >= 0) {
            $reflectionClass = new \ReflectionClass($class);
            $classFile = $reflectionClass->getFileName();
        } else {
            $pattern = sprintf(self::CLASS_DEFINITION_PATTERN, $class);

            $inclusionList = get_included_files();
            foreach ($inclusionList as $file) {
                $handle = @fopen($file, "r");
                if ($handle) {
                    while (($line = fgets($handle, 4096)) !== false) {
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
     * @param array <string> $list An array of elements
     * @return    string    The HTML code
     */
    static public function createTableFromList(&$list)
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;
        // $table = \Iresults\Core\Ui\Table::tableWithData($list);
        // $table->setUseFirstRowAsHeaderRow(FALSE);
        // return $table->render();

        $code = '';
        $entries = count($list);
        $maxColumns = 4;
        while ($maxColumns && ($entries / $maxColumns) < 1) {
            $maxColumns--;
        }
        if (!$maxColumns) {
            $maxColumns = 1;
        }

        if (!$isShell) {
            $code .= '<table style="font-size:11px; width=100%;">';
            $code .= '<tr>';
        }

        $currentColumn = 0;
        for ($i = 0; $i < $entries; $i++) {
            $element = $list[$i];
            if ($currentColumn >= $maxColumns) {
                if (!$isShell) {
                    $code .= '</tr><tr>';
                }
                $currentColumn = 0;
            }

            if (!$isShell) {
                $code .= "<td>$element</td>";
            } else {
                $code .= $element . PHP_EOL;
            }
            $currentColumn++;
        }

        if (!$isShell) {
            $code .= '</tr>';
            $code .= '</table>';
        }

        return $code;
    }

    /**
     * Outputs the given headline
     *
     * @param string $headline
     * @param int    $importance
     */
    static protected function _printHeadline($headline, $importance = 1)
    {
        $isShell = Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL;
        if (!$isShell) {
            Iresults::say("<h$importance>" . $headline . "</h$importance>");
        } else {
            $color = \Iresults\Core\Cli\ColorInterface::CYAN;
            $spaceBefore = 2;
            if ($importance == 2) {
                $color = \Iresults\Core\Cli\ColorInterface::BLUE;
                $spaceBefore = 1;
            }
            Iresults::say(str_repeat(PHP_EOL, $spaceBefore) . $headline . PHP_EOL, $color);
        }
    }

    /**
     * Wraps the given output in the ir_debug PRE-tag.
     *
     * @param string $output The output to wrap
     * @return    string    The wrapped output
     */
    static protected function _wrapIntoPre($output)
    {
        return Iresults::getEnvironment(
        ) === Iresults::ENVIRONMENT_SHELL ? $output : '<pre class="ir_debug">' . $output . '</pre>';
    }

    /**
     * Formats the given memory size
     *
     * @param int $size
     * @return string
     */
    static protected function _formatMemory($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        return @round($size / pow(1024, ($i = intval(floor(log($size, 1024))))), 2) . ' ' . $unit[$i];
    }

    /**
     * The shutdown function.
     *
     * @return    void
     */
    static public function shutdown()
    {
        self::$shutdownRun = true;

        foreach (self::$informationToList as $informationName) {
            $method = 'show' . ucfirst($informationName);
            call_user_func([__CLASS__, $method]);
        }
    }
}
