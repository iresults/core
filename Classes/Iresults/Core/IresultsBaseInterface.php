<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 02.10.13 16:28
 */


namespace Iresults\Core;


/**
 * Interface for the iresults framework's main class which provides common
 * methods which differ in different environments (frameworks)
 */
interface IresultsBaseInterface extends IresultsBaseConstants
{
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

    ########     ###    ######## ##     ##    ########  ########  ######   #######  ##       ##     ## ######## ####  #######  ##    ##
    ##     ##   ## ##      ##    ##     ##    ##     ## ##       ##    ## ##     ## ##       ##     ##    ##     ##  ##     ## ###   ##
    ##     ##  ##   ##     ##    ##     ##    ##     ## ##       ##       ##     ## ##       ##     ##    ##     ##  ##     ## ####  ##
    ########  ##     ##    ##    #########    ########  ######    ######  ##     ## ##       ##     ##    ##     ##  ##     ## ## ## ##
    ##        #########    ##    ##     ##    ##   ##   ##             ## ##     ## ##       ##     ##    ##     ##  ##     ## ##  ####
    ##        ##     ##    ##    ##     ##    ##    ##  ##       ##    ## ##     ## ##       ##     ##    ##     ##  ##     ## ##   ###
    ##        ##     ##    ##    ##     ##    ##     ## ########  ######   #######  ########  #######     ##    ####  #######  ##    ##

    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    /**
     * Returns the path to the base directory of the installation.
     *
     * @return    string
     */
    public function getBasePath();

    /**
     * Returns the base URL of the index.php file.
     *
     * @return    string
     */
    public function getBaseURL();

    /**
     * Returns the path to the temporary directory.
     *
     * @return    string
     */
    public function getTempPath();

    /**
     * Returns the path to the given package's directory
     *
     * @param string $package Package name
     * @return string
     */
    public function getPackagePath($package);

    /**
     * Returns the URL to the given package
     *
     * @param string $package Package name
     * @return string
     */
    public function getPackageUrl($package);

    /**
     * Returns the absolute path to the given resource
     *
     * @param Iresults\FS\FilesystemInterface|string $resource Either a filesystem instance or the path of a resource
     * @return string                                        The absolute path of the resource
     */
    public function getPathOfResource($resource);

    /**
     * Returns the URL of the resource.
     *
     * @param \Iresults\FS\FilesystemInterface|string $resource Either a filesystem instance or the path of a resource
     * @return    string                                            The URL of the resource
     */
    public function getUrlOfResource($resource);

    /**
     * Returns the versioned file path for the given file path, or the original
     * file path, if it doesn't exist.
     *
     * @param string $filePath The file path to create versions
     * @return    string
     */
    public function createVersionedFilePathForPath($filePath);


    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

    ######## ########     ###    ##    ##  ######  ##          ###    ######## ####  #######  ##    ##
    ##    ##     ##   ## ##   ###   ## ##    ## ##         ## ##      ##     ##  ##     ## ###   ##
    ##    ##     ##  ##   ##  ####  ## ##       ##        ##   ##     ##     ##  ##     ## ####  ##
    ##    ########  ##     ## ## ## ##  ######  ##       ##     ##    ##     ##  ##     ## ## ## ##
    ##    ##   ##   ######### ##  ####       ## ##       #########    ##     ##  ##     ## ##  ####
    ##    ##    ##  ##     ## ##   ### ##    ## ##       ##     ##    ##     ##  ##     ## ##   ###
    ##    ##     ## ##     ## ##    ##  ######  ######## ##     ##    ##    ####  #######  ##    ##

    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    /**
     * Returns the translated value for the given key.
     *
     * @param string $key       The key to translate
     * @param array  $arguments An optional array of arguments that will be passed to vsprintf()
     * @param string $package   Optional package name. If empty the package name will be automatically determined
     * @return    string
     */
    public function translate($key, array $arguments = [], $package = '');

    /**
     * Returns the current language and country code
     *
     * If the language can not be determined "en_US" (english) will be used.
     *
     * @return string
     */
    public function getLocale();


    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

    ########  ######## ########  ##     ##  ######    ######   #### ##    ##  ######        ####
    ##     ## ##       ##     ## ##     ## ##    ##  ##    ##   ##  ###   ## ##    ##      ##  ##
    ##     ## ##       ##     ## ##     ## ##        ##         ##  ####  ## ##             ####
    ##     ## ######   ########  ##     ## ##   #### ##   ####  ##  ## ## ## ##   ####     ####
    ##     ## ##       ##     ## ##     ## ##    ##  ##    ##   ##  ##  #### ##    ##     ##  ## ##
    ##     ## ##       ##     ## ##     ## ##    ##  ##    ##   ##  ##   ### ##    ##     ##   ##
    ########  ######## ########   #######   ######    ######   #### ##    ##  ######       ####  ##

    ##        #######   ######    ######   #### ##    ##  ######
    ##       ##     ## ##    ##  ##    ##   ##  ###   ## ##    ##
    ##       ##     ## ##        ##         ##  ####  ## ##
    ##       ##     ## ##   #### ##   ####  ##  ## ## ## ##   ####
    ##       ##     ## ##    ##  ##    ##   ##  ##  #### ##    ##
    ##       ##     ## ##    ##  ##    ##   ##  ##   ### ##    ##
    ########  #######   ######    ######   #### ##    ##  ######

    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    /**
     * Logs the given variable.
     *
     * @param mixed   $var     If $var is a scalar it will be written directly, else the output of var_export() is used
     * @param integer $code    The error code
     * @param string  $logfile The path to the log file. The default path is /typo3conf/iresults.log
     * @return    boolean                TRUE on success otherwise FALSE
     */
    public function log($var, $code = -1, $logfile = -1);

    /**
     * Dumps a given variable (or the given variables) wrapped into a 'pre' tag.
     *
     * @param mixed $var1
     * @return    string The printed content
     */
    public function pd($var1 = '__iresults_pd_noValue');

    /**
     * Outputs the given string, taking the current environment into account
     *
     * @param string  $message     The message to output
     * @param string  $color       An optional ASCII color to apply
     * @param boolean $insertBreak Insert a line break
     * @return    void
     */
    public function say($message, $color = null, $insertBreak = true);

    /**
     * Sets the debug renderer used by pd()
     *
     * @param integer|Iresults::RENDERER $debugRenderer The debug renderer as one of the RENDERER constants
     * @return    integer|Iresults::RENDERER    Returns the former configuration
     */
    public function setDebugRenderer($debugRenderer);

    /**
     * Returns if debugging is enabled in the current situation.
     *
     * @return    boolean
     */
    public function willDebug();

    /**
     * Set the static $willDebug to the given flag.
     *
     * @param boolean $flag
     * @return    boolean    Returns the former setting
     */
    public function forceDebug($flag = true);

    /**
     * Returns if the path information will be displayed
     *
     * @return boolean
     */
    public function getDisplayDebugPath();

    /**
     * Returns the current trace level.
     *
     * The starting depth to determine the file and line number of the original function call in pd().
     *
     * @return integer
     */
    public function getTraceLevel();

    /**
     * Sets the current trace level.
     *
     * The starting depth to determine the file and line number of the original function call in pd().
     *
     * @param int $newTraceLevel
     * @return int Returns the previous value
     */
    public function setTraceLevel($newTraceLevel);

    /**
     * Returns a description of the given value.
     *
     * @param mixed $value The value to describe
     * @return    string    The description text
     */
    public function descriptionOfValue($value);

    /**
     * Returns the name of the extension from which the iresults method was
     * called.
     *
     * You shouldn't use this in an production environment
     *
     * @param boolean $lowerCaseUnderscored Set to TRUE if you want the returned value to be in lower_case_underscored
     * @return    string
     */
    public function getNameOfCallingPackage($lowerCaseUnderscored = false);

    /**
     * Returns the configuration as an array.
     *
     * If a key is given, the configuration is searched for an entry with the
     * given key. If a matching entry exists it will be returned, otherwise
     * FALSE. If no key is given, the whole configuration array will be
     * returned.
     *
     * @param string $key The key for a configuration entry
     * @return    array|mixed    The whole configuration array, or the key's entry or FALSE for an unfound key
     */
    public function getConfiguration($key = null);

    /**
     * Overwrite the configuration at the given key with the new value.
     *
     * @param string $key   The key of the configuration to change
     * @param mixed  $value The new configuration value
     * @return    void
     */
    public function setConfiguration($key, $value);


    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

    ######## ##    ## ##     ## #### ########   #######  ##    ## ##     ## ######## ##    ## ########
    ##       ###   ## ##     ##  ##  ##     ## ##     ## ###   ## ###   ### ##       ###   ##    ##
    ##       ####  ## ##     ##  ##  ##     ## ##     ## ####  ## #### #### ##       ####  ##    ##
    ######   ## ## ## ##     ##  ##  ########  ##     ## ## ## ## ## ### ## ######   ## ## ##    ##
    ##       ##  ####  ##   ##   ##  ##   ##   ##     ## ##  #### ##     ## ##       ##  ####    ##
    ##       ##   ###   ## ##    ##  ##    ##  ##     ## ##   ### ##     ## ##       ##   ###    ##
    ######## ##    ##    ###    #### ##     ##  #######  ##    ## ##     ## ######## ##    ##    ##

    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    /**
     * Returns the environment.
     *
     * @return    integer|Iresults::ENVIRONMENT    This run's environment
     */
    public function getEnvironment();

    /**
     * Returns the protocol used for the current request.
     *
     * @return    string    Returns 'http', 'https' or whatever protocol was used for the current request
     */
    public function getProtocol();

    /**
     * Returns the output format as one of the OUTPUT_FORMAT constants.
     *
     * @return    string|Iresults::OUTPUT_FORMAT
     */
    public function getOutputFormat();

    /**
     * Returns the main framework the Iresults framework is used with.
     *
     * @return    Iresults::FRAMEWORK|string    The main framework
     */
    public function getFramework();

    /**
     * Returns if the current request is a full request (i.e. not an AJAX
     * request)
     *
     * @return    boolean
     */
    public function isFullRequest();


    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

    ######## ##     ##  ######  ######## ########  ######## ####  #######  ##    ##       ###    ##    ## ########
    ##        ##   ##  ##    ## ##       ##     ##    ##     ##  ##     ## ###   ##      ## ##   ###   ## ##     ##
    ##         ## ##   ##       ##       ##     ##    ##     ##  ##     ## ####  ##     ##   ##  ####  ## ##     ##
    ######      ###    ##       ######   ########     ##     ##  ##     ## ## ## ##    ##     ## ## ## ## ##     ##
    ##         ## ##   ##       ##       ##           ##     ##  ##     ## ##  ####    ######### ##  #### ##     ##
    ##        ##   ##  ##    ## ##       ##           ##     ##  ##     ## ##   ###    ##     ## ##   ### ##     ##
    ######## ##     ##  ######  ######## ##           ##    ####  #######  ##    ##    ##     ## ##    ## ########

    ######## ########  ########   #######  ########     ##     ##    ###    ##    ## ########  ##       #### ##    ##  ######
    ##       ##     ## ##     ## ##     ## ##     ##    ##     ##   ## ##   ###   ## ##     ## ##        ##  ###   ## ##    ##
    ##       ##     ## ##     ## ##     ## ##     ##    ##     ##  ##   ##  ####  ## ##     ## ##        ##  ####  ## ##
    ######   ########  ########  ##     ## ########     ######### ##     ## ## ## ## ##     ## ##        ##  ## ## ## ##   ####
    ##       ##   ##   ##   ##   ##     ## ##   ##      ##     ## ######### ##  #### ##     ## ##        ##  ##  #### ##    ##
    ##       ##    ##  ##    ##  ##     ## ##    ##     ##     ## ##     ## ##   ### ##     ## ##        ##  ##   ### ##    ##
    ######## ##     ## ##     ##  #######  ##     ##    ##     ## ##     ## ##    ## ########  ######## #### ##    ##  ######

    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    # MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    /**
     * The iresults exception handler.
     *
     * This method will be used for exception handling in CLI environment.
     *
     * @param \Exception $exception The exception to handle
     * @param boolean    $graceful  Set to TRUE if the handler should not stop the PHP script
     * @return        void
     */
    public function handleException($exception, $graceful = false);
}