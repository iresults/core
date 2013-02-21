<?php
namespace Iresults\Core\Model;

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
 * A class to convert colors.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Model
 */
class Color extends \Iresults\Core\Core {
	/**
	 * Hue
	 * @var float
	 */
	protected $hue = 0.0;

	/**
	 * Saturation
	 * @var float
	 */
	protected $saturation = 0.0;

	/**
	 * Brightness
	 * @var float
	 */
	protected $lightness = 0.0;

	/**
	 * Alpha
	 * @var float
	 */
	protected $alpha = 1.0;

	/**
	 * The constructor
	 *
	 * @param	mixed   $color The color to transform into an object
	 * @return	\Iresults\Core\Model\Color
	 */
	public function __construct($color) {
		if (is_string($color)) {
			if ($color[0] === '#' || preg_match('/[a-z]/i', $color)) { // hex
				list($this->hue, $this->saturation, $this->lightness) = self::rgbToHsl(self::hexToRgba($color));
			} else { // RGB string
				$color = str_replace(' ', ',', $color);
				$color = str_replace(',,', ',', $color);
				$color = explode(',', $color);
				list($this->hue, $this->saturation, $this->lightness) = self::rgbToHsl($color);
			}
		} else if(is_array($color)) {
			list($this->hue, $this->saturation, $this->lightness) = self::rgbToHsl($color);
		}

		return $this;
	}

	/**
	 * Returns the hue
	 *
	 * @return float
	 */
	public function getHue() {
	    return $this->hue;
	}

	/**
	 * Returns the saturation
	 *
	 * @return float
	 */
	public function getSaturation() {
	    return $this->saturation;
	}

	/**
	 * Returns the lightness
	 *
	 * @return float
	 */
	public function getLightness() {
	    return $this->lightness;
	}

	/**
	 * Returns the brightness
	 *
	 * @return float
	 */
	public function getBrightness() {
		return $this->lightness;
	}

	/**
	 * Returns the alpha
	 *
	 * @return float
	 */
	public function getAlpha() {
	    return $this->alpha;
	}

	/**
	 * Returns the color as a HSL data array.
	 *
	 * @return array
	 */
	public function asHSL() {
		return array(
			'H' => $this->hue,
			'S' => $this->saturation,
			'L' => $this->lightness,
			'A' => $this->alpha,
		);
	}

	/**
	 * Returns the color as a HSB data array.
	 *
	 * @return array
	 */
	public function asHSB() {
		return array(
			'H' => $this->hue,
			'S' => $this->saturation,
			'B' => $this->lightness,
			'A' => $this->alpha,
		);
	}

	/**
	 * Returns the color as a HEX color string with alpha, but without the sharp
	 * character ('#') at the beginning.
	 *
	 * @return string
	 */
	public function asRGB() {
		return self::hsvToRgb(array($this->hue, $this->saturation, $this->lightness));
	}

	/**
	 * Returns the color as a HEX color string without the sharp character ('#')
	 * at the beginning.
	 *
	 * @return string
	 */
	public function asHex() {
		return self::rgbaToHex($this->asRGB(), FALSE);
	}

	/**
	 * Returns the color as a HEX color string with alpha, but without the sharp
	 * character ('#') at the beginning.
	 *
	 * @return string
	 */
	public function asHexA() {
		return self::rgbaToHex($this->asRGB(), TRUE);
	}

	/**
	 * The string representation is the hex string.
	 *
	 * @return string
	 */
	public function __toString() {
		return '' . $this->asHexA();
	}

	/**
	 * Returns the HSL data
	 *
	 * @return array<float>
	 */
	public function getData() {
		return $this->asHsl();
	}

	/**
	 * Converts the HSV to RGB.
	 *
	 * @param  array  $hsv The HSV color array
	 * @return array<float>
	 */
	static public function hsvToRgb(array $hsv) {
		list($H, $S, $V) = $hsv;
		//1
		$H *= 6;
		//2
		$I = floor($H);
		$F = $H - $I;
		//3
		$M = $V * (1 - $S);
		$N = $V * (1 - $S * $F);
		$K = $V * (1 - $S * (1 - $F));
		//4
		switch ($I) {
			case 0:
				list($R,$G,$B) = array($V,$K,$M);
				break;
			case 1:
				list($R,$G,$B) = array($N,$V,$M);
				break;
			case 2:
				list($R,$G,$B) = array($M,$V,$K);
				break;
			case 3:
				list($R,$G,$B) = array($M,$N,$V);
				break;
			case 4:
				list($R,$G,$B) = array($K,$M,$V);
				break;
			case 5:
			case 6: //for when $H=1 is given
				list($R,$G,$B) = array($V,$M,$N);
				break;
		}
		return array(
			0       => $R,
			1       => $G,
			2       => $B,

			'red'   => $red,
			'green' => $green,
			'blue'  => $blue
		);
	}

	/**
	 * Convert a RGB colors into a HSL array.
	 *
	 * Thanks to Hofstadler Andi http://www.php.net/manual/en/function.imagecolorsforindex.php#86198
	 *
	 * @param  float|array<float> 	$red   If the first argument is an array, it is treated as an array of RGB values
	 * @param  float  				$green
	 * @param  float  				$blue
	 * @return array<float>
	 */
	static public function rgbToHsl($red, $green = 0.0, $blue = 0.0){
	 	if (is_array($red)) {
	 		$rgbArray = $red;
	 		$red 	= $rgbArray[0];
	 		$green 	= $rgbArray[1];
	 		$blue 	= $rgbArray[2];
	 	}
		$clrR = ($red / 255);
		$clrG = ($green / 255);
		$clrB = ($blue / 255);

		$clrMin = min($clrR, $clrG, $clrB);
		$clrMax = max($clrR, $clrG, $clrB);
		$deltaMax = $clrMax - $clrMin;

		$L = ($clrMax + $clrMin) / 2;

		if (0 == $deltaMax) {
			$H = 0;
			$S = 0;
		} else {
			if (0.5 > $L){
				$S = $deltaMax / ($clrMax + $clrMin);
			} else {
				$S = $deltaMax / (2 - $clrMax - $clrMin);
			}
			$deltaR = ((($clrMax - $clrR) / 6) + ($deltaMax / 2)) / $deltaMax;
			$deltaG = ((($clrMax - $clrG) / 6) + ($deltaMax / 2)) / $deltaMax;
			$deltaB = ((($clrMax - $clrB) / 6) + ($deltaMax / 2)) / $deltaMax;
			if ($clrR == $clrMax){
				$H = $deltaB - $deltaG;
			} else if ($clrG == $clrMax) {
				$H = (1 / 3) + $deltaR - $deltaB;
			} else if ($clrB == $clrMax) {
				$H = (2 / 3) + $deltaG - $deltaR;
			}
			if (0 > $H) $H += 1;
			if (1 < $H) $H -= 1;
		}
		return array($H, $S, $L);
	}

	/**
	 * Converts a hex color to RGBA.
	 * @param  string 		$hex 	The hex color string
	 * @return array<float>			The array of floats
	 */
	static public function hexToRgba($hex) {
		if ($hex[0] === '#') {
			$hex = substr($hex, 1);
		}
		$red   = '00';
		$green = '00';
		$blue  = '00';
		$alpha = 'FF';

		$hexLength = strlen($hex);
		switch ($hexLength) {
			case 3:
				$red   = $hex[0];
				$green = $hex[1];
				$blue  = $hex[2];
				break;

			case 4:
				$red   = $hex[0];
				$green = $hex[1];
				$blue  = $hex[2];
				$alpha = $hex[3];
				break;

			case 6:
				$red   = substr($hex, 0, 2);
				$green = substr($hex, 2, 2);
				$blue  = substr($hex, 4, 2);
				break;

			case 8:
				$red   = substr($hex, 0, 2);
				$green = substr($hex, 2, 2);
				$blue  = substr($hex, 4, 2);
				$alpha = substr($hex, 6, 2);
				break;

			default:
				break;
		}

		return array(
			0       => hexdec($red),
			1       => hexdec($green),
			2       => hexdec($blue),
			3       => hexdec($alpha),

			'red'   => hexdec($red),
			'green' => hexdec($green),
			'blue'  => hexdec($blue),
			'alpha' => hexdec($alpha),
		);
	}

	/**
	 * Converts a RGBA color to hex.
	 *
	 * @param  	array<float> 	$rgba 		The RGBA color array
	 * @param 	boolean 		$withAlpha	Indicates if the alpha should be added to the output
	 * @return string						The hex color string
	 */
	static public function rgbaToHex($rgba, $withAlpha = TRUE) {
		$hexString = dechex($rgba[0]) . dechex($rgba[1]) . dechex($rgba[2]);
		if ($withAlpha) {
			if (count($rgba) > 3) {
				$hexString .= dechex($rgba[3]);
			} else {
				$hexString .= '00';
			}
		}
		return $hexString;
	}
}
