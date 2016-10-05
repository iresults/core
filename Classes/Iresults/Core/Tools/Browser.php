<?php
namespace Iresults\Core\Tools;

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
 * The iresults browser class provides functions to retrieve and check the
 * clients browser.
 *
 * The class determines the clients browser through navigator.userAgent.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Tools
 * @version       1.0.0
 */
class Browser extends \Iresults\Core\Model
{
    protected $host = '';

    protected $accept = array();

    protected $cacheControl = '';

    protected $pragma = '';

    protected $userAgent = '';

    protected $acceptLanguage = '';

    protected $acceptEncoding = array();

    protected $cookie = '';

    protected $connection = '';

    protected $os = '';

    protected $platform = '';

    protected $browser = '';

    protected $version = '';

    protected $name = '';


    static public $knownBrowsers = array(
        'IE',
        'MSIE',
        'Firefox',
        'Chrome',
        'Safari',
        'Webkit',
        'Opera',
        'Netscape',
        'Konqueror',
        'Gecko',
    );


    /**
     * The constructor
     *
     * @param    array $parameters Optional parameters to pass to the constructor
     */
    public function __construct(array $parameters = array())
    {
        parent::__construct($parameters);

        $agent = $_SERVER['HTTP_USER_AGENT'];

        $this->os = $this->_getPlatform($agent);
        $this->platform = $this->os;

        $this->browser = $this->_browserInfo($agent);
    }

    /**
     * Returns the browser information as a dictionary.
     *
     * @return    array<Browser => Version>
     */
    public function getBrowser()
    {
        return $this->browser;
    }

//	\Iresults\Core\Tools\Browser.prototype.getName = function() {
//	this.parseName();
//	return this.name;
//}
//
///**
// * Returns the browser version.
// *
// * @return	{string} The detected browser version
// */
//\Iresults\Core\Tools\Browser.prototype.getVersion = function() {
//	this.parseVersion();
//	return $this->version;
//}


    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /* Function taken from robert@broofa.com at
     * http://www.php.net/manual/en/function.get-browser.php#92310.
     * Thanks a lot tho robert@broofa.com!
     */
    /**
     * Die Methode bietet einen Ersatz für die in PHP integrierte Funktion get_browser().
     *
     * @param    unknown_type $agent
     * @return    multitype:|multitype:NULL
     */
    protected function _browserInfo($agent = null)
    {
        // Declare known browsers to look for
        $known = self::$knownBrowsers;
        $knownLC = array();
        foreach ($known as $knownBrowser) {
            $knownLC[] = strtolower($knownBrowser);
        }


        // Clean up agent and build regex that matches phrases for known browsers
        // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
        // version numbers.E.g. "2.0.0.6" is parsed as simply "2.0"
        $agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#i';

        // Find all phrases (or return empty array if none found)
        if (!preg_match_all($pattern, $agent, $matches)) {
            return array();
        }


        // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
        // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
        // in the UA).That's usually the most correct.
        $oldBrowserId = 1000;
        foreach ($matches['browser'] as $browserString) {
            if (array_search($browserString, $knownLC) < $oldBrowserId) {
                $oldBrowserId = array_search($browserString, $knownLC);
            }
        }
        $this->name = self::$knownBrowsers[$oldBrowserId];
        $version = $this->_parseVersion($agent);

        return array($this->name => $version);
    }

    /**
     * Parses the version of the client's browser.
     *
     * @param    string $agent The agent string
     * @return    string    Returns the detected version
     */
    protected function _parseVersion($agent)
    {
        if ($this->browser) {
            return;
        }
        /*
         * 5.0 (Macintosh; U; Intel Mac OS X 10_6_4; en-us) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5
         *
         * 5.0 (Windows; U; Windows NT 6.0; en-us) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5
         *
         * 5.0 (iPhone; U; CPU iPhone OS 4_0_2 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A400 Safari/6531.22.7
         *
         * 5.0 (iPad; U; CPU OS 3_2_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B500 Safari/531.21.10
         *
         * 4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)
         *
         * 4.0 (compatible; MSIE 7.0; Windows NT 6.0)
         *
         * 4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)
         *
         * 5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3
         *
         * 5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9
         *
         * 9.80 (Macintosh; Intel Mac OS X; U; en) Presto/2.5.24 Version/10.53
         *
         * 5.0 (Macintosh; U; Intel Mac OS X 10_6_4; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Iron/5.0.381.0 Chrome/5.0.381 Safari/533.4
         *
         */
        $results = array();
        $pattern = "";
        $name = $this->name;
        $i = 'i';

        if ($name == 'Safari' || $name == 'MobileSafari') { // Pattern 1: "Version/5.0.2" => Safari, MobileSafari
            $pattern = '!Version\/[\d|.]*!i';
            if (!preg_match_all($pattern, $agent, $results)) {
                return;
            }
            if (!$results) {
                return;
            }

            $this->version = str_replace(array('Version/', 'version/'), '', $results[0][0]);

        } elseif ($name == 'IE' || $name == 'MSIE') { // Pattern 2: "MSIE 7.0" => IE
            $pattern = '!MSIE [0-9]*!i';
            if (!preg_match_all($pattern, $agent, $results)) {
                return;
            }

            $this->version = str_replace(array('MSIE ', 'msie '), '', $results[0][0]);

        } elseif ($name == 'Firefox' || $name == 'Chrome') { // Pattern 3: "Firefox/3.6.3" => Firefox, Chrome, Iron
            $pattern = '/' . $name . '\/[\d|.]*/i';
            if (!preg_match_all($pattern, $agent, $results)) {
                return;
            }

            $this->version = str_replace(array($name . '/', strtolower($name) . '/'), '', $results[0][0]);

        } elseif ($name == 'Opera') { // Pattern 4: "9.80 " => Opera
            $pattern = '!\/[\d|.]*!i';
            if (!preg_match_all($pattern, $agent, $results)) {
                return;
            }

            $this->version = $results[0][0];

        } else {
            $msg = 'Bad browser name "' . $name . '"';
            trigger_error($msg, E_USER_WARNING);
        }

        return $this->version;
    }

    /**
     * Die Methode ermittelt das Betriebssystem des Clients.
     *
     * @param    string $agent
     * @return    string
     */
    protected function _getPlatform($agent = null)
    {
        $agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
        // Running on what platform?
        if (preg_match('/linux/', $agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/', $agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/', $agent)) {
            $platform = 'win';
        } else {
            $platform = 'unrecognized';
        }

        // Overwrite if iPod or iPhone
        if (preg_match('/iphone/', $agent)) {
            $platform = 'iphone';
        }
        if (preg_match('/ipod/', $agent)) {
            $platform = 'ipod';
        }

        return $platform;
    }

    /**
     * Returns the clients operating system.
     *
     * @return    string
     */
    public function getPlatform()
    {
        return $this->_getPlatform();
    }

    /**
     * @see getPlatform()
     */
    public function getOs()
    {
        return $this->_getPlatform();
    }


    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //
    // STATICS
    //
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Returns the browser name.
     *
     * @return    string The detected browser name
     */
    static public function getName()
    {
        $browser = self::makeInstance();
        $browserInfo = $browser->getBrowser();

        return key($browserInfo);
    }

    /**
     * Returns the browser version.
     *
     * @return    string The detected browser version
     */
    static public function getVersion()
    {
        $browser = self::makeInstance();
        $browserInfo = $browser->getBrowser();

        return current($browserInfo);
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode überprüft ob das Client-OS dem übergebenen Parameter entspricht.
     *
     * @param    string $osSpec mac|win|linux|iphone|ipod
     * @return    boolean|boolean
     */
    static public function osIsFromString($osSpec)
    {
        static $sharedInstance;
        if (!$sharedInstance) {
            $sharedInstance = new self();
        }
        $os = $sharedInstance->os;
        if ($os == $osSpec) {
            return (bool)true;
        } else {
            return (bool)false;
        }
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode überprüft ob das Client-OS Macintosh ist.
     *
     * @return    boolean
     */
    static public function osIsMac()
    {
        return self::osIsFromString('mac');
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode überprüft ob das Client-OS Windows ist.
     *
     * @return    boolean
     */
    static public function osIsWin()
    {
        return self::osIsFromString('win');
    }

    static public function osIsWindows()
    {
        return self::osIsFromString('win');
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode überprüft ob das Client-OS Linux ist.
     *
     * @return    boolean
     */
    static public function osIsLinux()
    {
        return self::osIsFromString('linux');
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode überprüft ob das Client-Gerät ein iPhone ist.
     *
     * @return    boolean
     */
    static public function osIsIphone()
    {
        return self::osIsFromString('iphone');
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode überprüft ob das Client-Gerät ein iPod ist.
     *
     * @return    boolean
     */
    static public function osIsIpod()
    {
        return self::osIsFromString('ipod');
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode überprüft ob der Browser dem übergebenen String entspricht.
     *
     * @param    string $browserStr msie|firefox|safari|webkit|opera|netscape|konqueror|gecko
     * @return    boolean
     */
    static public function browserIsFromString($browserStr)
    {
        $known = self::$knownBrowsers;
        if (!in_array($browserStr, $known)) {
            return (bool)false;
        }

        $client = new self();
        $browser = $client->browser;
        if ($browser == $browserStr) {
            return (bool)true;
        } else {
            return (bool)false;
        }
    }

    /**
     * Returns the shared instance.
     *
     * @return    \Iresults\Core\Tools\Browser    The shared instance
     */
    static public function makeInstance()
    {
        static $sharedInstance;
        if (!$sharedInstance) {
            $sharedInstance = new self();
        }

        return $sharedInstance;
    }



    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    //MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Die Methode gibt den User-Agent-String zurück.
     *
     * @return    string
     */
    static public function getClientString()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}
