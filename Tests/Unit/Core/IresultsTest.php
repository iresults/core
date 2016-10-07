<?php
/*
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 02.10.13 16:07
 */


namespace Iresults\Core\Tests\Unit\Core;

require_once __DIR__ . '/../Autoloader.php';

use Iresults\Core\Iresults;
use Iresults\Core\Tests\Fixture\IresultsTestImplementation;

class IresultsTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
        Iresults::_destroySharedInstance();
        Iresults::_registerImplementationClassName('\\Iresults\\Core\\Base');
    }

    /**
     * @test
     */
    public function getSharedInstanceTest()
    {
        $this->assertInstanceOf('\\Iresults\\Core\\IresultsBaseInterface', Iresults::getSharedInstance());
        $this->assertInstanceOf('\\Iresults\\Core\\Base', Iresults::getSharedInstance());
    }

    /**
     * @test
     */
    public function overrideImplementationTest()
    {
        Iresults::_destroySharedInstance();

        Iresults::_registerImplementationClassName(IresultsTestImplementation::class);

        $this->assertInstanceOf('\\Iresults\\Core\\IresultsBaseInterface', Iresults::getSharedInstance());
        $this->assertInstanceOf(IresultsTestImplementation::class, Iresults::getSharedInstance());

        $this->assertFalse(Iresults::isFullRequest());
    }

    /**
     * @test
     */
    public function overrideImplementationWithGlobalTest()
    {
        $this->assertInstanceOf('\\Iresults\\Core\\Base', Iresults::getSharedInstance());
        Iresults::_destroySharedInstance();

        $GLOBALS['IRESULTS_REGISTERED_IMPLEMENTATION_CLASS'] = IresultsTestImplementation::class;

        $this->assertInstanceOf('\\Iresults\\Core\\IresultsBaseInterface', Iresults::getSharedInstance());
        $this->assertInstanceOf(IresultsTestImplementation::class, Iresults::getSharedInstance());

        $this->assertFalse(Iresults::isFullRequest());
    }

    /**
     * @test
     */
    public function overrideImplementationAndCallMagicMethodTest()
    {
        $this->assertInstanceOf('\\Iresults\\Core\\Base', Iresults::getSharedInstance());
        Iresults::_destroySharedInstance();

        Iresults::_registerImplementationClassName(IresultsTestImplementation::class);

        $this->assertInstanceOf('\\Iresults\\Core\\IresultsBaseInterface', Iresults::getSharedInstance());
        $this->assertInstanceOf(IresultsTestImplementation::class, Iresults::getSharedInstance());

        $this->assertTrue(Iresults::dynamicFunction());
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Exception\UndefinedMethod
     */
    public function callInvalidMagicMethodTest()
    {
        Iresults::dynamicFunction();
    }


    /**
     * @test
     */
    public function getBasePathTest()
    {
        $testPath = realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/';
        $this->assertEquals($testPath, Iresults::getBasePath());
    }

    /**
     * @test
     */
    public function getBaseURLTest()
    {
        $testPath = dirname($_SERVER['SCRIPT_NAME']) . '/';
        $this->assertEquals($testPath, Iresults::getBaseURL());
    }

    /**
     * @test
     */
    public function getTempPathTest()
    {
        $testPath = sys_get_temp_dir() . '/';
        $this->assertEquals($testPath, Iresults::getTempPath());
    }

    /**
     * @test
     */
    public function getPackagePathTest()
    {
        $package = 'WhateverPackage';
        $this->assertEquals('', Iresults::getPackagePath($package));
    }

    /**
     * @test
     */
    public function getPackageUrlTest()
    {
        $package = 'WhateverPackage';
        $this->assertEquals('', Iresults::getPackageUrl($package));
    }

    /**
     * @test
     */
    public function getPathOfResourceTest()
    {
        $resource = 'Resources/Public/JavaScript/main.js';
        $this->assertEquals((string)$resource, Iresults::getPathOfResource($resource));
    }

    /**
     * @test
     */
    public function getUrlOfResourceTest()
    {
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']) . '/';

        $resourceRelativePath = 'Resources/Public/JavaScript/main.js';
        $resourceAbsolutePath = $baseUrl . 'Resources/Public/JavaScript/main.js';


        $this->assertEquals($resourceRelativePath, Iresults::getUrlOfResource($resourceRelativePath));
        $this->assertEquals($baseUrl . $resourceRelativePath, Iresults::getUrlOfResource($resourceAbsolutePath));
    }

    /**
     * @test
     */
    public function createVersionedFilePathForPathTest()
    {
        $pathToExistingFile = __FILE__;
        $pathToNewFile = substr($pathToExistingFile, 0, -4) . '_1' . substr($pathToExistingFile, -4);
        $this->assertEquals($pathToNewFile, Iresults::createVersionedFilePathForPath($pathToExistingFile));
    }

    /**
     * @test
     */
    public function translateTest()
    {
        $key = 'test';
        $this->assertEquals('test', Iresults::translate($key));
    }

    /**
     * @test
     */
    public function getLocaleTest()
    {
        $this->assertEquals('en_US', Iresults::getLocale());
    }

    /**
     * @test
     */
    public function logTest()
    {
        $this->assertFalse(Iresults::log('whatever'));
    }

    /**
     * @test
     */
    public function pdTest()
    {
        $this->assertEquals('', Iresults::pd('whatever'));
    }

    /**
     * @test
     */
    public function sayTest()
    {
        // $this->markTestSkipped('Say can not be tested');
        // $this->assertEquals('fffffff', Iresults::say('whatever', $color = NULL, $insertBreak = TRUE));
    }

    /**
     * @test
     */
    public function setDebugRendererTest()
    {
        Iresults::setDebugRenderer(Iresults::RENDERER_VAR_DUMP);
        $this->assertEquals(Iresults::RENDERER_VAR_DUMP, Iresults::setDebugRenderer(Iresults::RENDERER_ZEND_DEBUG));
        $this->assertEquals(Iresults::RENDERER_ZEND_DEBUG, Iresults::setDebugRenderer(Iresults::RENDERER_ZEND_DEBUG));
    }

    /**
     * @test
     */
    public function willDebugTest()
    {
        $this->assertFalse(Iresults::willDebug());
    }

    /**
     * @test
     */
    public function forceDebugTest()
    {
        $this->assertFalse(Iresults::willDebug());
        $this->assertFalse(Iresults::forceDebug());
        $this->assertTrue(Iresults::willDebug());
    }

    /**
     * @test
     */
    public function getDisplayDebugPathTest()
    {
        $this->assertFalse(Iresults::getDisplayDebugPath());
    }

    /**
     * @test
     */
    public function getTraceLevelTest()
    {
        $this->assertEquals(-1, Iresults::getTraceLevel());
    }

    /**
     * @test
     */
    public function setTraceLevelTest()
    {
        $newTraceLevel = 200;
        $this->assertEquals(-1, Iresults::setTraceLevel($newTraceLevel));
        $this->assertEquals($newTraceLevel, Iresults::getTraceLevel());
    }

    /**
     * @test
     */
    public function descriptionOfValueTest()
    {
        $testValue1 = 'hallo';
        $testValue2 = array('hallo');
        $testValue3 = array('message' => 'hallo');
        $testValue4 = (object)array('message' => 'hallo');
        $testValue5 = 10;
        $this->assertEquals('hallo', Iresults::descriptionOfValue($testValue1));
        $this->assertEquals("Array(\n\thallo\n)", Iresults::descriptionOfValue($testValue2));
        $this->assertEquals("Array(\n\tmessage: hallo\n)", Iresults::descriptionOfValue($testValue3));
        $this->assertEquals('<stdClass>', Iresults::descriptionOfValue($testValue4));
        $this->assertEquals('10', Iresults::descriptionOfValue($testValue5));
    }

    /**
     * @test
     */
    public function getNameOfCallingPackageTest()
    {
        $this->assertFalse(Iresults::getNameOfCallingPackage(false));
        $this->assertFalse(Iresults::getNameOfCallingPackage(true));
    }

    /**
     * @test
     */
    public function getConfigurationTest()
    {
        $this->assertNull(Iresults::getConfiguration());
    }

    /**
     * @test
     */
    public function setConfigurationTest()
    {
        $key = 'testSuccessFull';
        $value = 'Yes';
        $this->assertNull(Iresults::getConfiguration());
        Iresults::setConfiguration($key, $value);
        $this->assertEquals($value, Iresults::getConfiguration($key));
        $this->assertEquals(array($key => $value), Iresults::getConfiguration());
    }

    /**
     * @test
     */
    public function getEnvironmentTest()
    {
        $testEnvironment = php_sapi_name() === 'cli' ? Iresults::ENVIRONMENT_CLI : Iresults::ENVIRONMENT_WEB;
        $this->assertEquals($testEnvironment, Iresults::getEnvironment());
    }

    /**
     * @test
     */
    public function getProtocolTest()
    {
        $this->assertEquals(
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http',
            Iresults::getProtocol()
        );
    }

    /**
     * @test
     */
    public function getOutputFormatTest()
    {
        $testFormat = Iresults::OUTPUT_FORMAT_JSON;

        // The output format is always binary in CLI mode
        if ((isset($_SERVER['TERM']) && $_SERVER['TERM'])
            || php_sapi_name() === 'cli'
        ) {
            $testFormat = Iresults::OUTPUT_FORMAT_BINARY;
        }

        $_GET['format'] = 'json';
        $this->assertEquals($testFormat, Iresults::getOutputFormat());

        // The output format is not mutable
        $_GET['format'] = 'anythingOther';
        $this->assertEquals($testFormat, Iresults::getOutputFormat());
    }

    /**
     * @test
     */
    public function getFrameworkTest()
    {
        $this->markTestSkipped('PHPUnit uses Symfony components');
    }

    /**
     * @test
     */
    public function isFullRequestTest()
    {
        $this->assertTrue(Iresults::isFullRequest());
    }
}
