<?php
namespace Iresults\Core\Tests\Locale;

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

use Iresults\Core\Locale\Environment;
use Iresults\Core\Locale\TranslatorFactory;

require_once __DIR__ . '/../Autoloader.php';

/**
 * Test case for the locale translator
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Iresults_Locale
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase {
	static protected $systemLocale;

	public function setUp() {
		if (!self::$systemLocale) {
			self::$systemLocale = Environment::getSharedInstance()->getLocale();
		}
	}

	public function tearDown() {
		Environment::getSharedInstance()->setLocale(self::$systemLocale);
	}

	/**
	 * @test
	 */
	public function getOriginalMessageTest(){
		$message = 'A simple translated string';
		$translator = TranslatorFactory::translatorWithSource(__DIR__ . '/');
		$this->assertEquals($message, $translator->translate($message));
	}

	/**
	 * @test
	 */
	public function getTranslatedMessageTest(){
		$originalMessage = 'A simple translated string';
		$translatedMessage = 'Ein einfacher übersetzter Text';
		$translator = TranslatorFactory::translatorWithSource(__DIR__ . '/');
		$this->assertEquals($translatedMessage, $translator->translate($originalMessage, NULL, 'de_DE'));

		$this->assertEquals($originalMessage, $translator->translate($originalMessage));
	}

	/**
	 * @test
	 */
	public function getOriginalMessageWithArgumentTest(){
		$message = 'My name is %s';
		$translator = TranslatorFactory::translatorWithSource(__DIR__ . '/');
		$this->assertEquals('My name is Daniel', $translator->translate($message, array('Daniel')));
	}

	/**
	 * @test
	 */
	public function getTranslatedMessageWithArgumentTest(){
		$originalMessage = 'My name is %s';
		$translatedMessage = 'Mein Name ist Daniel';
		$translator = TranslatorFactory::translatorWithSource(__DIR__ . '/');
		$this->assertEquals($translatedMessage, $translator->translate($originalMessage, array('Daniel'), 'de_DE'));
	}

	/**
	 * @test
	 */
	public function getTranslatedMessageWithUtf8LocaleTest(){
		$originalMessage = 'My name is %s';
		$translatedMessage = 'Mein Name ist Daniel';
		$translator = TranslatorFactory::translatorWithSource(__DIR__ . '/');
		$this->assertEquals($translatedMessage, $translator->translate($originalMessage, array('Daniel'), 'de_DE.UTF-8'));
	}

	/**
	 * @test
	 */
	public function getTranslatedMessageWithEnvironmentLocaleTest(){
		$originalMessage = 'A simple translated string';
		$translatedMessage = 'Ein einfacher übersetzter Text';
		$translator = TranslatorFactory::translatorWithSource(__DIR__ . '/');
		$this->assertEquals($originalMessage, $translator->translate($originalMessage));

		Environment::getSharedInstance()->setLocale('de_DE.UTF-8');
		$this->assertEquals($translatedMessage, $translator->translate($originalMessage));
	}

	/**
	 * @test
	 */
	public function localeBindingTest(){
		$originalMessage = 'A simple translated string';
		$translatedMessage = 'Ein einfacher übersetzter Text';
		$translator = TranslatorFactory::translatorWithSource(__DIR__ . '/');
		$this->assertEquals($originalMessage, $translator->translate($originalMessage));

		// Set de_DE
		$translator->bindToLocale('de_DE.UTF-8');
		$this->assertEquals($translatedMessage, $translator->translate($originalMessage));

		// Still de_DE
		Environment::getSharedInstance()->setLocale('en_US');
		$this->assertEquals($translatedMessage, $translator->translate($originalMessage));

		// Set to en_US
		$translator->bindToLocale('en_US');
		$this->assertEquals($originalMessage, $translator->translate($originalMessage));

	}
}
?>
