<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 05.10.16 12:02
 */


namespace Iresults\Core\Tests;


class Bootstrap
{
    public function run()
    {
        $this->registerCustomAutoloader();
        $this->registerComposerAutoloader();
    }

    private function registerComposerAutoloader()
    {
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        } elseif (file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
            require_once __DIR__ . '/../../../../vendor/autoload.php';
        } else {
            throw new \Exception('Could not find composer autoloader');
        }
    }

    private function registerCustomAutoloader()
    {
        spl_autoload_register(
            function ($className) {
                $pathRelative = str_replace(
                    ['_', '\\'],
                    DIRECTORY_SEPARATOR,
                    $className
                );
                $classFile = __DIR__ . '/../Classes/' . $pathRelative . '.php';
                if (file_exists($classFile)) {
                    require_once $classFile;
                }
            }
        );

    }
}

(new Bootstrap())->run();
