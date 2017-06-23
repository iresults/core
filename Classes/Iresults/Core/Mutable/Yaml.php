<?php

namespace Iresults\Core\Mutable;


/**
 * The concrete implementation class for mutable objects that read data from YAML
 * files using Zend_Config_Yaml.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Mutable
 */
class Yaml extends \Iresults\Core\Mutable
{


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Factory method: Returns a mutable object representing the data from the
     * given URL.
     *
     * @param string $url URL of the file to read
     * @return    \Iresults\Core\Mutable
     */
    static public function mutableWithContentsOfUrl($url)
    {
        $mutable = null;
        if (class_exists('\Symfony\Component\Yaml\Yaml')) { // Symfony YAML
            $mutable = new \Iresults\Core\Mutable\Yaml\SymfonyYaml();
            $mutable->initWithContentsOfUrl($url);
        } else {
            throw new \Exception('No concrete implementation for "\Iresults\Core\Mutable\Yaml" found', 1320674794);
        }

        return $mutable;
    }
}
