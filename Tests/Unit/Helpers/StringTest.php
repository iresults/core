<?php
namespace Iresults\Core\Tests\Helpers;

/*
 * Copyright notice
 *
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *             Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Test case for the String Tool class.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Iresults_Tools
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class StringTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
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