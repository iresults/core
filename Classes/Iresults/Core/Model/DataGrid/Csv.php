<?php
namespace Iresults\Core\Model\DataGrid;

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



/**
 * The concrete implementation class for mutable objects that read data from a
 * CSV file.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Mutable
 */
class Csv extends \Iresults\Core\Mutable {
	/**
	 * The property key used to store the attributes array, if
	 * $useAtNotationForAttributes is disabled.
	 */
	const ATTRIBUTES_PROPERTY_KEY = 'attributes';

	/**
	 * @var boolean If set to TRUE attributes will be set as properties and
	 * their keys will be prefixed with an "@". If set to FALSE attributes will
	 * be collected in an array and stored with the key "attributes".
	 */
	static protected $useAtNotationForAttributes = FALSE;

	/**
	 * @var boolean If set to TRUE a new \Iresults\Core\Mutable will be used for
	 * self closing tags, if they don't own attributes.
	 */
	static protected $useEmptyMutableForSelfClosingTags = FALSE;

	/**
	 * @var boolean If set to TRUE the property keys will be converted to
	 * upperCamelCase with the first character lower case. If this is FALSE only
	 * the first character will be made lower case.
	 */
	static protected $makeKeysUpperCamelCase = TRUE;

	/**
	 * Indicates if the first row should be skipped.
	 *
	 * @var boolean
	 */
	protected $ignoreFirst = FALSE;

	/**
	 * An array containing the first row of the read file if $ignoreFirst is set to TRUE.
	 *
	 * @var array<string>
	 */
	protected $headerRow = array();

	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* INITIALIZATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Initialize the instance with the contents of the given URL.
	 *
	 * @param	string	$url Path to the XML file
	 * @return	\Iresults\Core\Mutable\Csv
	 */
	public function initWithContentsOfUrl($url) {
		$this->_addXmlDataToObject($xmlDoc,$this);

		//$this->pd($this);

		return $this;
	}

	/**
	 * Reads the contents of the import file into an array of dictionaries. Each
	 * row is represented as an element of the array.
	 *
	 * @param	string	$url The URL of the file to import
	 * @return	void
	 */
	protected function _parseFile($url) {
		$passedFirst = FALSE;
		$limit = -1;
		$counter = 0;

		$fh = fopen($url, 'r');
		if ($fh === FALSE) throw new Exception("Couldn't open the file '$url' for reading.", 1318410706);

		while ( ( ($csvData = fgetcsv($fh, 1000, ",")) !== FALSE ) && ($limit == -1 || $counter++ <= $limit) ) {
			if ($passedFirst == FALSE && $this->ignoreFirst) {
				$passedFirst = TRUE;
				$this->headerRow = $csvData;
				continue;
			}

			$import = array();

			//$this->pd($csvData);

			/**
			 * Prepare the import data.
			 */
			$import['sku'] =	trim($csvData[0]);
			$import['typ'] =	trim($csvData[1]);
			$import['type'] =	trim($csvData[1]);
			$import['de'] =		trim($csvData[2]);
			$import['en'] =		trim($csvData[3]);
			$import['fr'] =		trim($csvData[4]);

			/**
			 * Prepare the surface.
			 */
			if (trim($csvData[5]) != '') {
				$import['oberflaeche'] = $this->getIdForSurfaceName(trim($csvData[5]));
			} else {
				$import['oberflaeche'] = '';
			}

			/**
			 * Prepare the color.
			 */
			$csvData[6]=str_replace("ö","oe",$csvData[6]);
			$csvData[6]=str_replace("ü","ue",$csvData[6]);
			$csvData[6]=str_replace("é","e",$csvData[6]);
			if (trim($csvData[6]) != '') {
				$import['farbe'] = $this->getIdForColorName(trim($csvData[6]));
			} else {
				$import['farbe'] = '';
			}


			/**
			 * Set the attribute set ID.
			 */
			$import['attribute_set_id'] = $this->getAttributeSetIdForData($import);

			/**
			 *
			 */
			$import['laenge_ca_mm'] =		trim($csvData[7]);
			$import['laenge_mm'] =			trim($csvData[8]);
			$import['laenge_cm'] =			trim($csvData[9]);
			$import['laenge_m'] =			trim($csvData[10]);
			$import['breite_mm'] =			trim($csvData[11]);
			$import['breite_cm'] =			trim($csvData[12]);
			$import['durchmesser_mm'] =		trim($csvData[13]);
			$import['durchmesser_ca_mm'] = 	trim($csvData[14]);
			$import['aussen_mm'] =			trim($csvData[15]);
			$import['innen_mm'] =			trim($csvData[16]);
			$import['drahtstaerke_mm'] =	trim($csvData[17]);
			$import['drahtstaerke_ca_mm'] =	trim($csvData[18]);
			$import['platte_ca_mm'] =		trim($csvData[19]);
			$import['oese_mm'] =			trim($csvData[20]);
			$import['kugel_mm'] =			trim($csvData[21]);
			$import['schale_mm'] =			trim($csvData[22]);
			$import['schuessel_mm'] =		trim($csvData[23]);
			$import['bajonett_mm'] =		trim($csvData[24]);
			$import['inhalt'] =				trim($csvData[25]);
			$import['inhalt_ca'] =			trim($csvData[26]);
			$import['chf'] =				trim($csvData[27]);
			$import['eur'] =				trim($csvData[28]);
			$import['url'] = 				urlencode(str_replace(' ','-',trim($import['de'])));


			if (count($csvData) >= 30 && trim($csvData[29])) {
				$import['packung'] = $this->getAttributeValueIdForValueName(trim($csvData[29]));
			} else {
				$import['packung'] = 'no_selection';
			}

			$this->_data[] = $import;
		}
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* XML TRAVERSING        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Sets the data from the given XML node as the properties of the mutable
	 * object.
	 *
	 * @param	SimpleXMLElement    $data   The XML node to process
	 * @param	\Iresults\Core\Mutable	$object The object whose properties to set
	 * @return	void
	 */
	protected function _addXmlDataToObject($data, \Iresults\Core\Mutable $object) {
		/**
		 * Loop through all children of the data node.
		 */
		foreach ($data as $key => $currentChild) {
			$key = $this->_prepareKeyOfNode($key, $currentChild);
			/**
			 * Check if the key is already set.
			 */
			if ($object->getObjectForKey($key)) {
				$oldChild = $object->getObjectForKey($key);
				if (!is_array($oldChild) || $oldChild instanceof Traversable) {
					$oldChild = array($oldChild);
				}
				$oldChild[] = $this->_getRepresentationForNode($currentChild);
				$object->setObjectForKey($key, $oldChild);
			} else {
				$object->setObjectForKey($key, $this->_getRepresentationForNode($currentChild));
			}
		}
	}

	/**
	 * Collects all the attributes of the given node and sets it as the objects
	 * attribute-property.
	 *
	 * @param	SimpleXMLElement    $data   The XML node to process
	 * @param	\Iresults\Core\Mutable	$object The object whose property to set
	 * @return	void
	 */
	protected function _addXmlAttributesToObject($data, \Iresults\Core\Mutable $object) {
		if (!is_object($data)) return;

		$attributesXml = $data->attributes();
		if ($attributesXml->count()) {
			$attributes = array();
			foreach ($attributesXml as $key => $attribute) {
				$key = $this->_prepareKeyOfNode($key, $node);
				if (self::$useAtNotationForAttributes) { // Set the value directly
					$object->setObjectForKey("@".$key, $this->_getRepresentationForNode($attribute));
				} else { // Store it in the attributes array
					$attributes[$key] = $this->_getRepresentationForNode($attribute);
				}
			}
			if (!self::$useAtNotationForAttributes) // Set the attributes array as property with the key 'attributes'
				$object->setObjectForKey(self::ATTRIBUTES_PROPERTY_KEY, $attributes);
		}


	}

	/**
	 * Returns a representation of the given XML node which may be set as the
	 * property of the mutable object.
	 *
	 * @param	SimpleXMLElement	$node The node to get a representation of
	 * @return	mixed
	 */
	protected function _getRepresentationForNode($node) {
		/**
		 * If the given node is not an object do nothing.
		 */
		if (!is_object($node)) {
			return $node;
		}

		//\Iresults\Core\Iresults::pd($node, $node->count());
		//if ($node->getName() == "wohnung") {
		//	$this->pd($node->getName(), $node, $node->count(), $node->children());
		//}


		/**
		 * Check if the node is a scalar and doesn't have attributes attached.
		 */
		if ($node->count() === 0 && $node->attributes()->count() == 0) {
			//echo "go 1{$node->getName()} $node<br>";
			$node = $this->_createEndPointOfNode($node);
		} else
		/**
		 * Check if the node is a scalar but has attributes attached.
		 */
		if ($node->count() === 0 && $node->attributes()->count() != 0) {
			//echo "go 2{$node->getName()}<br>";
			$newNodeObject = $this->_createSubObjectForNode($node);
			$this->_addXmlAttributesToObject($node, $newNodeObject);
			$newNodeObject->setObjectForKey("value",$this->_createEndPointOfNode($node));
			$node = $newNodeObject;
		} else
		/**
		* It is an other node -> create a new mutable for it.
		*/
		if ($node->count() > 0) {
			//echo "go 3{$node->getName()}<br>";
			$newNodeObject = $this->_createSubObjectForNode($node);
			$this->_addXmlDataToObject($node, $newNodeObject);
			$this->_addXmlAttributesToObject($node, $newNodeObject);
			$node = $newNodeObject;
		} else {
			$this->pd("Couldn't handle node name '{$node->getName()}'");
		}
		return $node;
	}

	/**
	 * Prepares the key.
	 *
	 * The method converts the key according to the configuration in
	 * makeKeysUpperCamelCase. If it is TRUE the key will be converted to
	 * upperCamelCase with the first character lower case. If this is FALSE only
	 * the first character will be made lower case.
	 * The node which is identified by the given key is passed too, this may be
	 * used in subclasses.
	 *
	 * @param	string	$key The key to prepare
	 * @param	SimpleXMLElement	$node The node of the given key
	 * @return	string
	 */
	protected function _prepareKeyOfNode($key, $node) {
		$key = str_replace(array('/',',','|','\\'),'_',$key);
		$key = t3lib_div::makeInstance('t3lib_cs')->specCharsToASCII('utf-8',$key);
		if (self::$makeKeysUpperCamelCase) {
			return $this->_lcfirst(t3lib_div::underscoredToUpperCamelCase($key));
		}
		return $this->_lcfirst($key);
	}

	/**
	 * Returns a end point value of the given node. If the node is a scalar it
	 * will be returned. If it is empty either a new empty instance of this
	 * class will be returned or TRUE if useEmptyMutableForSelfClosingTags is
	 * disabled.
	 *
	 * @param	SimpleXMLElement	$node The node
	 * @return	mixed
	 */
	protected function _createEndPointOfNode($node) {
		//if (!("$node")) {
		//	$this->pd("node",$node,"$node",$node->getName());
		//}

		if ("$node" === "0") {
			$node = 0.0;
		} else
		if (!("$node")) { // Handle self closing tags without arguments.
			if (self::$useEmptyMutableForSelfClosingTags) {
				$node = $this->_createSubObjectForNode($node);
			} else {
				$node = TRUE;
			}
		} else
		if (substr("$node",0,1) !== "0" && is_numeric("$node")) {
			$node = (float)$node;
		} else {
			$node = "$node";
		}
		return $node;
	}

	/**
	 * Creates and returns a new sub object.
	 *
	 * @param	SimpleXMLElement	$node The corresponding XML node is passed for
	 * handling in subclasses.
	 *
	 * @return	\Iresults\Core\Mutable\Xml|object
	 */
	protected function _createSubObjectForNode($node) {
		$newNodeObject = NULL;
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			$newNodeObject = new static();
		} else {
			$class = get_class($this);
			$newNodeObject = new $class();
		}
		return $newNodeObject;
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* OVERRIDE THE GETTER   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns a properties data
	 * @param	string	$name
	 * @return	mixed
	 */
	public function __get($name) {
		return parent::__get($name);



		$value = parent::__get($name);

		/**
		 * If the value is NULL try to fetch an associated attribute.
		 */
		if (is_null($value)) {
			/**
			 * If the first character isn't an "@" character
			 */
			if (self::$useAtNotationForAttributes && substr($name,0,1) != '@') {
				$value = $this->__get("@$name");
			}
			/* else if (!self::$useAtNotationForAttributes && substr($name,0,1) == '@') {
				$name = substr($name,1);
				$attributes = $this->__get(self::ATTRIBUTES_PROPERTY_KEY);
				if ($attributes && isset($attributes[$name])) {
					$value = $attributes[$name];
				}
			}
			/* */
		}
		return $value;
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* CONFIGURATION         MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Sets the configuration of useAtNotationForAttributes.
	 *
	 * @param	boolean	$flag
	 * @return	void
	 */
	static public function setUseAtNotation($flag) {
		self::$useAtNotationForAttributes = $flag;
	}

	/**
	 * Returns the configuration of useAtNotationForAttributes.
	 *
	 * @return	boolean    The current value of self::$useAtNotationForAttributes
	 */
	static public function getUseAtNotation() {
		return self::$useAtNotationForAttributes;
	}

	/**
	 * Sets the configuration of makeKeysUpperCamelCase.
	 *
	 * @param	boolean	$flag
	 * @return	void
	 */
	static public function setMakeKeysUpperCamelCase($flag) {
		self::$makeKeysUpperCamelCase = $flag;
	}

	/**
	 * Returns the configuration of makeKeysUpperCamelCase.
	 *
	 * @return	boolean    The current value of self::$makeKeysUpperCamelCase
	 */
	static public function getMakeKeysUpperCamelCase() {
		return self::$makeKeysUpperCamelCase;
	}

	/**
	 * Sets the configuration of useEmptyMutableForSelfClosingTags.
	 *
	 * @param	boolean	$flag
	 * @return	void
	 */
	static public function setUseEmptyMutableForSelfClosingTags($flag) {
		self::$makeKeysUpperCamelCase = $flag;
	}

	/**
	 * Returns the configuration of useEmptyMutableForSelfClosingTags.
	 *
	 * @return	boolean    The current value of self::useEmptyMutableForSelfClosingTags
	 */
	static public function getUseEmptyMutableForSelfClosingTags() {
		return self::useEmptyMutableForSelfClosingTags;
	}


}