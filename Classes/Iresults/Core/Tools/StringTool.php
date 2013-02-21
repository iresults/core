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
 * The iresults string tool class provides a common interface to work with strings.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Tools
 * @version 1.0.0
 */
class StringTool {
	/**
	 * Strings will not be converted.
	 */
	const FORMAT_KEEP = 0;

	/**
	 * Strings will be converted to UpperCamelCase.
	 */
	const FORMAT_LOWER_CAMEL_CASE = 1;

	/**
	 * Strings will be converted to lowerCamelCase.
	 */
	const FORMAT_UPPER_CAMEL_CASE = 2;

	/**
	 * Strings will be converted to underscored.
	 */
	const FORMAT_UNDERSCORED = 3;

	/**
	 * Regular expression pattern to find string after the last word.
	 */
	const PATTERN_FIND_STRING_AFTER_LAST_WORD = '/[\s|\W]+[^\s]*$/ui';

	/**
	 * Transforms the given string to the given format.
	 *
	 * @param	string	$string The string to transform
	 * @param	integer	$format The format to transform into as one of the FORMAT constants
	 * @return	string    The transformed string
	 */
	static public function transformStringToFormat($string, $format) {
		switch($format) {
			case self::FORMAT_LOWER_CAMEL_CASE:
				$string = self::underscoredToLowerCamelCase($string);
				break;

			case self::FORMAT_UPPER_CAMEL_CASE:
				$string = self::underscoredToUpperCamelCase($string);
				break;

			case self::FORMAT_UNDERSCORED:
				$string = self::camelCaseToLowerCaseUnderscored($string);
				break;

			case self::FORMAT_KEEP:
			default:
				break;
		}
		return $string;
	}

	/**
	 * Returns the given string converted from camel case to lower case underscored.
	 *
	 * @param	string	$string The string to convert
	 * @return	string	The converted string
	 */
	static public function camelCaseToLowerCaseUnderscored($string) {
		if (defined('TYPO3_MODE')) {
			return \t3lib_div::camelCaseToLowerCaseUnderscored($string);
		}
		return self::strtolower(preg_replace('/(?<=\w)([A-Z])/', '_\\1', $string));
	}

	/**
	 * Returns the given string converted from underscores to upper camel case.
	 *
	 * @param	string	$string The string to convert
	 * @return	string	The converted string
	 */
	static public function underscoredToUpperCamelCase($string) {
		if (defined('TYPO3_MODE')) {
			return \t3lib_div::underscoredToUpperCamelCase($string);
		}
		$upperCamelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', self::strtolower($string))));
		return $upperCamelCase;
	}

	/**
	 * Returns the given string converted from underscores to camel case with the first character lower case.
	 *
	 * @param	string	$string The string to convert
	 * @return	string	The converted string
	 */
	static public function underscoredToLowerCamelCase($string) {
		if (defined('TYPO3_MODE')) {
			return \t3lib_div::underscoredToLowerCamelCase($string);
		}
		$upperCamelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', self::strtolower($string))));
		$lowerCamelCase = self::lcfirst($upperCamelCase);
		return $lowerCamelCase;
	}

	/**
	 * Returns the given string transformed to lower case.
	 *
	 * @param	string	$string The string to transform
	 * @return	string    The transformed string
	 */
	static public function strtolower($string) {
		if (defined('TYPO3_MODE')) {
			return \t3lib_div::strtolower($string);
		}
		return strtolower($string);
	}

	/**
	 * Returns the given string transformed to upper case.
	 *
	 * @param	string	$string The string to transform
	 * @return	string    The transformed string
	 */
	static public function strtoupper($string) {
		if (defined('TYPO3_MODE')) {
			return \t3lib_div::strtoupper($string);
		}
		return strtoupper($string);
	}

	/**
	 * Returns the given string with the first character transformed to lower case.
	 *
	 * @param	string	$string The string to transform
	 * @return	string    The transformed string
	 */
	static public function lcfirst($string) {
		if (defined('TYPO3_MODE')) {
			return \t3lib_div::lcfirst($string);
		}
		if (IR_MODERN_PHP) {
			return lcfirst($string);
		}
		return self::strtolower($string[0]) . substr($string, 1);
	}

	/**
	 * Returns the given string with the first character transformed to lower case.
	 *
	 * @param	string	$string The string to transform
	 * @return	string    The transformed string
	 */
	static public function ucfirst($string) {
		return ucfirst($string);
	}

	/**
	 * Pad a string to a certain length with another string.
	 *
	 * An UTF-8 secure string pad function.
	 * Thanks to Kari http://www.php.net/manual/en/function.str-pad.php#89754
	 *
	 * @param	string	$input		The input string
	 * @param	integer	$length		The length to pad to
	 * @param	string	$padString	The string to add
	 * @param	STR_PAD	$padType	Optional pad type
	 * @return	string				The padded string
	 */
	static public function pad($input, $length, $padString = ' ', $padType = STR_PAD_RIGHT) {
		$diff = strlen($input) - strlen(utf8_decode($input));
		return str_pad($input, $length + $diff, $padString, $padType);
	}

	/**
	 * Transforms the method name into a property key.
	 *
	 * @param  string $methodName The method name
	 * @return string             Returns the property key, or FALSE if it couldn't be determined
	 */
	static public function methodToPropertyKey($methodName) {
		$accessor = substr($methodName, 0, 3);
		$propertyKey = substr($methodName, 3);
		if ($accessor === 'get' || $accessor === 'set') {
			return self::lcfirst($propertyKey);
		}
		return FALSE;
	}

	/**
	 * Guesses the length of the given text.
	 *
	 * Instead of just calling strlen the method checks looks for
	 * space-consuming ('M', 'A', 'R', etc.) and thin characters ('l', 'i', '!').
	 *
	 * Warning: experimental
	 *
	 * @param  	string 		$text 			The text to analyse
	 * @param 	float 		$maxWidth 		The maximum width the text will be cropped to
	 * @param 	string 		$croppedString 	Reference to the variable in which the cropped string will be stored in
	 * @param 	boolean 	$softCrop 		If set to TRUE the cropped string will be cropped after the last word
	 * @param 	boolean 	$sansSerif 		Set to FALSE if the text will be rendered with a serif font
	 * @return 	float       				The estimate length
	 */
	static public function guessWidthOfText($text, $maxWidth = -1, &$croppedString = '', $softCrop = FALSE, $sansSerif = TRUE) {
		$width = 0;
		$croppedString = '';
		$thinWidth = 41;
		$thinCharacters = 'fijlrtIJ!"/\\()`´*-\'.:;,¡“[]|{}';

		$middleWidth = 100;
		$middleCharacters = ' abcdeghknopqsuvxyzüöäßABCDEFGHKLNOPQRSTUVXYZÜÖÄ1234567890§$&=?#+¶¢≠¿';

		$wideWidth = 150;
		$wideCharacters = 'mwMW%<>_…';

		if (!$sansSerif) {
			$thinWidth = 55.5;
			$middleWidth = 80;
			$wideWidth = 155;
			$wideCharacters .= 'ABCDEFGHKLNOPQRTUVXYZ';
		}

		if ($maxWidth === -1) {
			$maxWidth = strlen($text) * 10;
		}
		$maxWidth *= 100;

		$characters = preg_split('/(?<!^)(?!$)/u', $text);
		while (($currentCharacter = current($characters)) !== FALSE) {
			if (mb_strpos($wideCharacters, $currentCharacter) !== FALSE) {
				$width += $wideWidth;
			} else if (mb_strpos($thinCharacters, $currentCharacter) !== FALSE) {
				$width += $thinWidth;
			} else {
				$width += $middleWidth;
			}

			// If the maximum width isn't reached add the current character
			if ($width < $maxWidth) {
				$croppedString .= $currentCharacter;
			}
			next($characters);
		}

		/*
		 * If the text is longer than the maximum width, find the last whitespace
		 * and crop the string to this position.
		 */
		if ($width >= $maxWidth && $softCrop) {
			$result = array();
			if (preg_match(self::PATTERN_FIND_STRING_AFTER_LAST_WORD, $croppedString, $result, PREG_OFFSET_CAPTURE)) {
				$lastWhitespacePosition = $result[0][1];
				$croppedString = mb_substr($croppedString, 0, $lastWhitespacePosition);
			}
		}
		return $width / 100;
	}

}
?>