<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 10:08
 */


namespace Iresults\Core;

/**
 * Interface providing constants for the iresults framework's main class
 *
 * @package Iresults\Core
 */
interface IresultsBaseConstants
{
    /**
     * The different debug renderer that can be used by pd().
     */
    const RENDERER = 'RENDERER';

    /**
     * Do not render inside the pd method.
     */
    const RENDERER_NONE = -1;

    /**
     * Render the variable information inside pd using the class Zend_Debug.
     */
    const RENDERER_ZEND_DEBUG = 1;

    /**
     * Render the variable information inside pd using var_dump().
     */
    const RENDERER_VAR_DUMP = 2;

    /**
     * Render the variable information inside pd using var_export().
     */
    const RENDERER_VAR_EXPORT = 3;

    /**
     * Render the variable information inside pd using the class Iresults_Debug.
     */
    const RENDERER_IRESULTS_DEBUG = 4;

    /**
     * Render the variable information inside pd using the class Kint (http://raveren.github.io/kint/)
     */
    const RENDERER_KINT = 5;

    /**
     * The different environment constants for web and shell or CLI.
     */
    const ENVIRONMENT = 'ENVIRONMENT';

    /**
     * The environment constant for a web server request.
     */
    const ENVIRONMENT_WEB = 1;

    /**
     * The environment for a shell/cli/terminal request.
     */
    const ENVIRONMENT_SHELL = 2;

    /**
     * The environment for a shell/cli/terminal request.
     */
    const ENVIRONMENT_CLI = 2;


    /**
     * The different output formats.
     */
    const OUTPUT_FORMAT = 'OUTPUT_FORMAT';

    /**
     * Some kind of XML data
     */
    const OUTPUT_FORMAT_XML = 'xml';

    /**
     * JSON encoded data
     */
    const OUTPUT_FORMAT_JSON = 'json';

    /**
     * Plain text data
     */
    const OUTPUT_FORMAT_PLAIN = 'plain';

    /**
     * Binary data
     */
    const OUTPUT_FORMAT_BINARY = 'bin';


    /**
     * The framework iresults is used with.
     */
    const FRAMEWORK = 'FRAMEWORK';

    /**
     * Iresults is used standalone.
     */
    const FRAMEWORK_STANDALONE = 'standalone';

    /**
     * Iresults is used in conjunction with TYPO3.
     */
    const FRAMEWORK_TYPO3 = 'typo3';

    /**
     * Iresults is used in conjunction with FLOW.
     */
    const FRAMEWORK_FLOW = 'flow';

    /**
     * Iresults is used in conjunction with Symfony.
     */
    const FRAMEWORK_SYMFONY = 'symfony';
}