<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 10:08
 */


namespace Iresults\Core\Standalone;

/**
 * Autoloader for Iresults core classes
 *
 * @tutorial
 * require_once 'Classes/Iresults/Core/Standalone/Autoloader.php';
 * \Iresults\Core\Standalone\Autoloader::register();
 */
class Autoloader
{
    /**
     * Defines if the Autoloader has been registered
     *
     * @var bool
     */
    static protected $isRegistered = false;

    /**
     * Registers the class to use for autoloading
     *
     * @param bool $prepend If TRUE, the Autoloader will be prepended on the autoload stack instead of being appended
     */
    static public function register($prepend = false)
    {
        if (self::$isRegistered) {
            return;
        }
        self::$isRegistered = spl_autoload_register([__CLASS__, 'autoload'], true, $prepend);
    }

    /**
     * Unregisters the class to use for autoloading
     */
    static public function unregister()
    {
        spl_autoload_unregister([__CLASS__, 'autoload']);
    }

    /**
     * Tries to autoload the given class
     *
     * @param string $className
     */
    static public function autoload($className)
    {
        if ($className[0] !== '\\') {
            $className = '\\' . $className;
        }
        if (substr($className, 0, 15) !== '\\Iresults\\Core\\') {
            return;
        }
        $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        require_once self::getClassBasePath() . $classFile;
    }

    /**
     * Returns the base path of the Iresults core files
     *
     * @return string
     */
    static protected function getClassBasePath()
    {
        static $classBasePath = '';
        if (!$classBasePath) {
            $classBasePath = __DIR__ . '/../../../';
        }

        return $classBasePath;
    }
}
