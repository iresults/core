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
 * The concrete implementation class for mutable objects that read data from a
 * CSV file with two columns.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Mutable
 */
class Csv extends \Iresults\Core\Mutable {
	/**
	 * Indicates if the first column of the CSV file should be treated as key
	 * path (values will be assigned through setObjectForKeyPath()) or as simple
	 * keys (values will be assigned through setObjectForKey()).
	 *
	 * @var boolean
	 */
	protected $treatFirstColumnAsKeyPath = FALSE;



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* INITIALIZATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Initialize the instance with the contents of the given URL
	 *
	 * @param	string	$url Path to the XML file
	 * @return	\Iresults\Core\Mutable\Xml
	 */
	public function initWithContentsOfUrl($url) {
		return $this->initWithContentsOfCSVFile($url);
	}

	/**
	 * Initializes the object with the contents from the CSV file at the given
	 * path
	 *
	 * @param	string	$filePath  The path to the file to load data from
	 * @param	string	$delimiter	The CSV field delimiter
	 * @param	string	$enclosure The CSV field enclosure character
	 * @param	string	$escape	\" The CSV files escape character
	 * @return	\Iresults\Core\Mutable\Csv   Returns the initialized object
	 */
	public function initWithContentsOfCSVFile($filePath, $delimiter = ',', $enclosure = '"', $escape = '\\') {
		$line = 0;
		$data = NULL;
		$grid = array();
		$lineString = NULL;
		$oldIniValue = FALSE;
		$treatFirstColumnAsKeyPathLocal = $this->treatFirstColumnAsKeyPath;

		static $maxLines = 100000;

		$oldIniValue = ini_set('auto_detect_line_endings', TRUE);

		$fh = fopen($filePath, 'r');
		if ($fh === FALSE) {
			throw new \UnexpectedValueException("Couldn't load CSV file from '$filePath'.", 1329750849);
		}

		/*
		 * Use str_getcsv() if it is available, because fgetcsv() doesn't
		 * seem to work with CSV-lines without enclosure.
		 */
		while (($lineString = fgets($fh)) !== FALSE && $line++ < $maxLines) {
			$row = str_getcsv($lineString, $delimiter, $enclosure, $escape);
			if (count($row) < 2) {
				throw new \UnexpectedValueException("Error in input file '$filePath': The row at line $line has less than two values.", 1329750916);
			}
			$keyPath = trim($row[0]);
			$value = trim($row[1]);

			if ($treatFirstColumnAsKeyPathLocal) {
				$this->setObjectForKeyPath($keyPath, $value);
			} else {
				$this->setObjectForKey($keyPath, $value);
			}

		}


		if (!feof($fh)) {
			throw new \LengthException("Unexpected end of line when reading CSV import file '$filePath'.", 1329750856);
		}

		fclose($fh);
		ini_set('auto_detect_line_endings', $oldIniValue);
		return $this;
	}

	/**
	 * Returns if the first column of the CSV file should be treated as key
	 * path (values will be assigned through setObjectForKeyPath()) or as simple
	 * keys (values will be assigned through setObjectForKey()).
	 *
	 * @return	boolean
	 */
	public function getTreatFirstColumnAsKeyPath() {
		return $this->treatFirstColumnAsKeyPath;
	}

	/**
	 * Sets if the first column of the CSV file should be treated as key
	 * path (values will be assigned through setObjectForKeyPath()) or as simple
	 * keys (values will be assigned through setObjectForKey()).
	 *
	 * @param	boolean	$flag
	 * @return	void
	 */
	public function setTreatFirstColumnAsKeyPath($flag) {
		$this->treatFirstColumnAsKeyPath = $flag;
	}
}
