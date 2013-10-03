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
	public function setUp() {
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function guessWidthOfTextSansSerif(){
		$text1 = str_repeat('lI', 50);
		$text2 = str_repeat('eA', 50);
		$text3 = str_repeat('mW', 50);

		$croppedString1 = '';
		$this->assertEquals(41, \Iresults\Core\Tools\StringTool::guessWidthOfText($text1, 15, $croppedString1, FALSE, TRUE));
		$this->assertEquals('lIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlI', $croppedString1);

		$croppedString2 = '';
		$this->assertEquals(100, \Iresults\Core\Tools\StringTool::guessWidthOfText($text2, 15, $croppedString2, FALSE, TRUE));
		$this->assertEquals('eAeAeAeAeAeAeA', $croppedString2);

		$croppedString3 = '';
		$this->assertEquals(150, \Iresults\Core\Tools\StringTool::guessWidthOfText($text3, 15, $croppedString3, FALSE, TRUE));
		$this->assertEquals('mWmWmWmWm', $croppedString3);
	}

	/**
	 * @test
	 */
	public function compareGuessedWidthOfSansSerifToSerif(){
		$text = str_repeat('ea', 50);

		$croppedSerif = '';
		$this->assertEquals(80, \Iresults\Core\Tools\StringTool::guessWidthOfText($text, 40, $croppedSerif, FALSE, FALSE));

		$croppedSansSerif = '';
		$this->assertEquals(100, \Iresults\Core\Tools\StringTool::guessWidthOfText($text, 40, $croppedSansSerif, FALSE, TRUE));

		$this->assertEquals('eaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeae', $croppedSerif);
		$this->assertEquals('eaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeae', $croppedSansSerif);

		// echo '<div style="font-family: Times,Arial,sans-serif; color: #fc0; font-size: 16px;">';
		// echo $croppedSerif;
		// echo '</div>';
		// echo '<br />';
		// echo '<div style="font-family: Arial,sans-serif; color: #fc0; font-size: 16px;">';
		// echo $croppedSansSerif;
		// echo '</div>';
	}

	/**
	 * @test
	 */
	public function guessWidthOfTextSerif(){
		$text1 = str_repeat('lI', 100);
		$text2 = str_repeat('ea', 100);
		$text3 = str_repeat('mW', 100);

		// $style = 'font-family: Georgia,Arial,sans-serif; color: #fc0; font-size: 16px;';
		// echo '<div style="' . $style . '">';
		// echo $text1;
		// echo '<br />';
		// echo $text2;
		// echo '<br />';
		// echo $text3;
		// echo '</div>';
		// echo '<br />';

		$croppedString1 = '';
		$this->assertEquals(111, \Iresults\Core\Tools\StringTool::guessWidthOfText($text1, 80, $croppedString1, FALSE, FALSE));
		$this->assertEquals('lIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlIlI', $croppedString1);

		$croppedString2 = '';
		$this->assertEquals(160, \Iresults\Core\Tools\StringTool::guessWidthOfText($text2, 80, $croppedString2, FALSE, FALSE));
		$this->assertEquals('eaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeaeae', $croppedString2);

		$croppedString3 = '';
		$this->assertEquals(310, \Iresults\Core\Tools\StringTool::guessWidthOfText($text3, 80, $croppedString3, FALSE, FALSE));
		$this->assertEquals('mWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWmWm', $croppedString3);


		// echo '<div style="' . $style . '">';
		// echo $croppedString1;
		// echo '<br />';
		// echo $croppedString2;
		// echo '<br />';
		// echo $croppedString3;
		// echo '</div>';
	}
}
?>