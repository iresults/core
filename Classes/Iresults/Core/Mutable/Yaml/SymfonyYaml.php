<?php

namespace Iresults\Core\Mutable\Yaml;

use Symfony\Component\Yaml\Yaml;


/**
 * The concrete implementation class for mutable objects that read data from YAML
 * files using Symfony (http://symfony.com/doc/current/components/yaml.html)
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Mutable
 */
class SymfonyYaml extends \Iresults\Core\Mutable\Yaml
{
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* INITIALIZATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Initialize the instance with the contents of the given URL.
     *
     * @param string $url The URL of the file to read
     * @return    \Iresults\Core\Mutable\Yaml
     */
    public function initWithContentsOfUrl($url)
    {
        $config = Yaml::parse($url);
        $this->initWithArray($config);

        return $this;
    }
}
