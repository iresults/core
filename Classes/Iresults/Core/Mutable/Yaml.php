<?php
namespace Iresults\Core\Mutable;

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
     * @param    string $url URL of the file to read
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
