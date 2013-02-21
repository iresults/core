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
 * XML file.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Mutable
 */
class Xml extends \Iresults\Core\Mutable {
	/**
	 * The property key used to store the attributes array, if
	 * $useAtNotationForAttributes is disabled.
	 */
	const ATTRIBUTES_PROPERTY_KEY = 'attributes';

	/**
	 * If set to TRUE attributes will be set as properties and their keys will
	 * be prefixed with an "@". If set to FALSE attributes will be collected in
	 * an array and stored with the key "attributes".
	 *
	 * @var boolean
	 */
	static protected $useAtNotationForAttributes = FALSE;

	/**
	 * If set to TRUE a new \Iresults\Core\Mutable will be used for self closing
	 * tags, if they don't own attributes.
	 *
	 * @var boolean
	 */
	static protected $useEmptyMutableForSelfClosingTags = FALSE;

	/**
	 * The format in which the property keys will be converted to.
	 * Defaults to \Iresults\Core\Tools\StringTool::FORMAT_UNDERSCORED.
	 *
	 * @var integer|\Iresults\Core\Tools\StringTool::FORMAT
	 */
	static protected $keyTransformFormat = \Iresults\Core\Tools\StringTool::FORMAT_UNDERSCORED;

	/**
	 * If set to TRUE nodes of type string will be trimmed.
	 *
	 * @var boolean
	 */
	static protected $trimStringInputs = TRUE;

	/**
	 * If set to TRUE child nodes that match the criteria, are automatically
	 * grouped into arrays even if they doesn't have any siblings.
	 *
	 * @var boolean
	 */
	static protected $automaticFeaturesEnabled = FALSE;

	/**
	 * Indicates if the environment is not UTF-8
	 *
	 * @var boolean
	 */
	static protected $noUTF8 = TRUE;



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* INITIALIZATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Initialize the instance with the contents of the given URL.
	 *
	 * @param	string	$url Path to the XML file
	 * @return	\Iresults\Core\Mutable\Xml
	 */
	public function initWithContentsOfUrl($url) {
		$xmlString = '';
		/*
		 * Check if the environment is UTF-8
		 */
		if (!self::$noUTF8 && (!isset($GLOBALS['TYPO3_CONF_VARS']) || $GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem'])) {
			$xmlDoc = simplexml_load_file($url);
		} else {
			self::$noUTF8 = TRUE;
			$file = \Iresults\Core\System\FileManager::sharedFileManager()->getResourceAtUrl($url);
			$xmlString = $this->_prepareXmlString($file->contents());
			$xmlDoc = simplexml_load_string($xmlString);
		}

		if (!$xmlDoc) {
			$xmlError = libxml_get_last_error();
			if ($xmlString) {# && strpos($xmlError->message, 'line ') !== FALSE) {
				$xmlDebugString = '';
				$xmlString = str_replace(array("\r", "\r\n", "\n"), PHP_EOL, $xmlString);
				$xmlStringParts = explode(PHP_EOL, $xmlString);
				foreach ($xmlStringParts as $lineNumber => $xmlLine) {
					$xmlDebugString .= '#' . ($lineNumber + 1) . ": \t $xmlLine" . PHP_EOL;
				}
				\Iresults\Core\Iresults::pd($xmlDebugString);
			}
			if ($xmlError === FALSE) {
				$xmlError = 'Couldn\'t detect the XML error (This may be because of a timeout or a firewall)';
				throw \Iresults\Core\Error::errorWithMessageCodeAndUserInfo($xmlError, 1337692752, array($xmlError));
			}
			throw \Iresults\Core\Error::errorWithMessageCodeAndUserInfo($xmlError->message, $xmlError->code, array($xmlError));
		}

		$this->_addXmlDataToObject($xmlDoc, $this, $xmlDoc->getName());
		return $this;
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* XML STRING REPRESENTATION    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns a string representation of the XML object.
	 *
	 * @return	string
	 */
	public function asXML() {
		$xmlString = '';
		foreach ($this as $name => $data) {
			$currentDataString = $data;
			if (is_object($data) && $data instanceof \Iresults\Core\Mutable\Xml) {
				$currentDataString = $data->asXml();
			} else if (is_array($data)) {
				$currentDataString = '';
				foreach ($data as $key => $value) {
					if (is_object($value) && $value instanceof \Iresults\Core\Mutable\Xml) {
						$currentDataString .= $value->asXML();
					} else {
						$currentDataString .= '<' . $key . '>' . PHP_EOL . $value . PHP_EOL . '</' . $key . '>';
					}
				}
			}
			$currentDataString = str_replace(array('<?xml version=\'1.0\'?>', '<?xml version="1.0"?>', '<ir_Iresults_mutable_xml_root>', '</ir_Iresults_mutable_xml_root>'), '', $currentDataString);
			$xmlString .= '<' . $name . '>' . $currentDataString . '</' . $name . '>' . PHP_EOL;
		}

		// Format the XML string
		$xmlString = trim($xmlString);
		if (class_exists('DOMDocument')) {
			$xmlString = '<?xml version=\'1.0\'?><ir_Iresults_mutable_xml_root>'  . $xmlString . '</ir_Iresults_mutable_xml_root>';
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = FALSE;
			$dom->formatOutput = TRUE;
			$dom->loadXML($xmlString);
			$xmlString = $dom->saveXML();
		} else {
			$xmlObject = simplexml_load_string('<?xml version=\'1.0\'?><ir_Iresults_mutable_xml_root>'  . $xmlString . '</ir_Iresults_mutable_xml_root>');
			if (is_object($xmlObject)) {
				$xmlString = $xmlObject->asXML();
			}
		}

		$xmlString = str_replace(array('<?xml version=\'1.0\'?>', '<?xml version="1.0"?>', '<ir_Iresults_mutable_xml_root>', '</ir_Iresults_mutable_xml_root>'), '', $xmlString);
		return $xmlString;
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
	 * @param	string              $objectName	 The node name of the given object
	 * @return	void
	 */
	protected function _addXmlDataToObject($data, \Iresults\Core\Mutable $object, $objectName = '') {
		$automaticFeaturesEnabledLocal = self::$automaticFeaturesEnabled;

		/*
		 * Loop through all children of the data node.
		 */
		foreach ($data as $key => $currentChild) {
			$key = $this->_prepareKeyOfNode($key, $currentChild);

			/*
			 * Check if the key is already set.
			 */
			if ($object->getObjectForKey($key)) {
				$oldChild = $object->getObjectForKey($key);
				if (!is_array($oldChild) || $oldChild instanceof Traversable) {
					$oldChild = array($oldChild);
				}
				$oldChild[] = $this->_getRepresentationForNode($currentChild);
				$object->setObjectForKey($key, $oldChild);
			/*
			 * Check if the automatic features should be applied.
			 */
			} else if ($objectName && $automaticFeaturesEnabledLocal &&
					  $this->_checkIfNamesIndicateThatTheChildIsACollection($objectName, $key)) {
				$collection = array();
				$collection[] = $this->_getRepresentationForNode($currentChild);
				$object->setObjectForKey($key, $collection);
			/*
			 * If is a single child.
			 */
			} else {
				$object->setObjectForKey($key, $this->_getRepresentationForNode($currentChild));
			}
		}

		unset($automaticFeaturesEnabledLocal);
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
					$object->setObjectForKey('@' . $key, $this->_getRepresentationForNode($attribute));
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
		/*
		 * If the given node is not an object do nothing.
		 */
		if (!is_object($node)) {
			return $node;
		}

		/*
		 * Check if the node is a scalar and doesn't have attributes attached.
		 */
		if ($node->count() === 0 && $node->attributes()->count() == 0) {
			$node = $this->_createEndPointOfNode($node);
		} else
		/*
		 * Check if the node is a scalar but has attributes attached.
		 */
		if ($node->count() === 0 && $node->attributes()->count() != 0) {
			$newNodeObject = $this->_createSubObjectForNode($node);
			$this->_addXmlAttributesToObject($node, $newNodeObject);
			$newNodeObject->setObjectForKey('value', $this->_createEndPointOfNode($node));
			$node = $newNodeObject;
		} else
		/*
		 * It is an other node -> create a new mutable for it.
		 */
		if ($node->count() > 0) {
			$newNodeObject = $this->_createSubObjectForNode($node);
			$nodeName = $this->_prepareKeyOfNode($node->getName(), $node);
			$this->_addXmlDataToObject($node, $newNodeObject, $nodeName);
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
	 * makeKeysUpperCamelCase. If it is TRUE and the key contains an underscore
	 * ("_"), the key will be converted to lowerCamelCase.
	 * If this is FALSE only the first character will be made lower case.
	 *
	 * The node which is identified by the given key is passed too, this may be
	 * used in subclasses.
	 *
	 * @param	string	$key The key to prepare
	 * @param	SimpleXMLElement	$node The node of the given key
	 * @return	string
	 */
	protected function _prepareKeyOfNode($key, $node) {
		$key = str_replace(array('/',',','|','\\'),'_',$key);
		$key = t3lib_div::makeInstance('t3lib_cs')->specCharsToASCII('utf-8', $key);
		if (self::$keyTransformFormat !== \Iresults\Core\Tools\StringTool::FORMAT_KEEP) {
			return \Iresults\Core\Tools\StringTool::transformStringToFormat($key, self::$keyTransformFormat);
		}
		return \Iresults\Core\Tools\StringTool::lcfirst($key);
	}

	/**
	 * Prepares the XML string before it is passed through simplexml_load_string().
	 *
	 * @param	string	$xmlString
	 * @return	string
	 */
	protected function _prepareXmlString($xmlString) {
		$xmlString = t3lib_div::makeInstance('t3lib_cs')->specCharsToASCII('utf-8', $xmlString);
		$xmlString = str_replace(array('encoding="UTF-8"?', 'encoding=\'UTF-8\'?'), 'encoding="ISO-8859-1"?', $xmlString);
		$xmlString = str_replace('<<', '&#171;;', $xmlString);
		$xmlString = str_replace('>>', '&#187;', $xmlString);
		return $xmlString;
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
		if ("$node" === '0') {
			$node = 0.0;
		} else
		if (!("$node")) { // Handle self closing tags without arguments.
			if (self::$useEmptyMutableForSelfClosingTags) {
				$node = $this->_createSubObjectForNode($node);
			} else {
				$node = TRUE;
			}
		} else
		if (substr("$node",0,1) !== '0' && is_numeric("$node")) {
			$node = (float)$node;
		} else if (self::$trimStringInputs) {
			$node = trim($node);
			if (self::$noUTF8) {
				$node = t3lib_div::makeInstance('t3lib_cs')->specCharsToASCII('utf-8', $node);
			}
		} else {
			$node = "$node";
			if (self::$noUTF8) {
				$node = t3lib_div::makeInstance('t3lib_cs')->specCharsToASCII('utf-8', $node);
			}
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
		if (IR_MODERN_PHP) {
			$newNodeObject = new static();
		} else {
			$class = get_class($this);
			$newNodeObject = new $class();
		}
		return $newNodeObject;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* HELPER METHODS        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Checks if the child should be a collection.
	 *
	 * This is always TRUE if a child already exists for the current key. If the
	 * configuration for automaticFeaturesEnabled is set to TRUE additional
	 * tests are performed.
	 *
	 * @see _checkIfNamesIndicateThatTheChildIsACollection()
	 *
	 * @param	\Iresults\Core\Mutable	$parent The parent node
	 * @param	mixed	$child The child node
	 * @param	string	$parentName The node name of the parent
	 * @param	string	$childName The node name of the child
	 * @return	boolean    Returns TRUE if the child should be an array
	 */
	protected function _checkIfChildShouldBeACollection($parent, $child, $parentName, $childName) {
		/*
		 * Check if the key is already set.
		 */
		if ($parent->getObjectForKey($childName)) {
			return TRUE;
		/*
		 * Check if the automatic features should be applied.
		 */
		} else if (self::$automaticFeaturesEnabled && $this->_checkIfNamesIndicateThatTheChildIsACollection()) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Returns if the node names indicate a collection.
	 *
	 * Test 1:
	 *  The name of the parent node equals the child node's name with an
	 *  attached 's'.
	 *  parent: 'examples'
	 *  child: 'example'
	 *
	 * Test 2:
	 *  The name of the parent node ends with 'ies' and the child node's name
	 *  matches with an ending 'y'.
	 *  parent: 'category'
	 *  child: 'categories'
	 *
	 * @param	string	$parentName The node name of the parent
	 * @param	string	$childName  The node name of the child
	 * @return	boolean    Returns TRUE if the child should be an array
	 */
	protected function _checkIfNamesIndicateThatTheChildIsACollection($parentName, $childName) {
		if (!$parentName || !$childName) {
			return FALSE;
		}

		/*
		 * Test 1:
		 */
		if ($parentName === $childName . 's') {
			return TRUE;
		}

		/*
		 * Test 2:
		 */
		if (substr($parentName, -3) === 'ies' && substr($childName, -1) === 'y' &&
		   substr($parentName, 0, -3) === substr($childName, 0, -1)) {
			return TRUE;
		}
		return FALSE;
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
		$value = parent::__get($name);
		if (is_null($value) && preg_match('!^[^A-Z].*[A-Z]!', $name)) {
			$name = \Iresults\Core\Tools\StringTool::camelCaseToLowerCaseUnderscored($name);
			$value = parent::__get($name);
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
	 * If $flag is TRUE the configuration of keyTransformFormat will be set to
	 * \Iresults\Core\Tools\StringTool::FORMAT_LOWER_CAMEL_CASE, otherwise to
	 * \Iresults\Core\Tools\StringTool::FORMAT_KEEP.
	 *
	 * @param	boolean	$flag
	 * @return	void
	 * @deprecated
	 */
	static public function setMakeKeysUpperCamelCase($flag) {
		if (IR_MODERN_PHP) {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated. Use setKeyTransformFormat() instead.', E_USER_DEPRECATED);
		} else {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated. Use setKeyTransformFormat() instead.', E_USER_WARNING);
		}
		if ($flag) {
			self::$keyTransformFormat = \Iresults\Core\Tools\StringTool::FORMAT_LOWER_CAMEL_CASE;
		} else {
			self::$keyTransformFormat = \Iresults\Core\Tools\StringTool::FORMAT_KEEP;
		}
	}

	/**
	 * Returns if the configuration of keyTransformFormat is
	 * \Iresults\Core\Tools\StringTool::FORMAT_LOWER_CAMEL_CASE.
	 *
	 * @return	boolean    TRUE if self::$keyTransformFormat is \Iresults\Core\Tools\StringTool::FORMAT_LOWER_CAMEL_CASE, otherwise FALSE
	 * @deprecated
	 */
	static public function getMakeKeysUpperCamelCase() {
		if (IR_MODERN_PHP) {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated. Use getKeyTransformFormat() instead.', E_USER_DEPRECATED);
		} else {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated. Use getKeyTransformFormat() instead.', E_USER_WARNING);
		}
		return self::$keyTransformFormat == \Iresults\Core\Tools\StringTool::FORMAT_LOWER_CAMEL_CASE;
	}

	/**
	 * Sets the configuration of keyTransformFormat.
	 *
	 * @param	integer|\Iresults\Core\Tools\StringTool::FORMAT $format The format to transform to
	 *
	 * @return	void
	 */
	static public function setKeyTransformFormat($format) {
		self::$keyTransformFormat = $format;
	}

	/**
	 * Returns the configuration of keyTransformFormat.
	 *
	 * @return	integer|\Iresults\Core\Tools\StringTool::FORMAT The current configuration
	 */
	static public function getKeyTransformFormat() {
		return self::$keyTransformFormat;
	}

	/**
	 * Sets the configuration of useEmptyMutableForSelfClosingTags.
	 *
	 * @param	boolean	$flag
	 * @return	void
	 */
	static public function setUseEmptyMutableForSelfClosingTags($flag) {
		self::$useEmptyMutableForSelfClosingTags = $flag;
	}

	/**
	 * Returns the configuration of useEmptyMutableForSelfClosingTags.
	 *
	 * @return	boolean    The current value of self::$useEmptyMutableForSelfClosingTags
	 */
	static public function getUseEmptyMutableForSelfClosingTags() {
		return self::$useEmptyMutableForSelfClosingTags;
	}

	/**
	 * Sets the configuration of trimStringInputs.
	 *
	 * @param	boolean	$flag
	 * @return	void
	 */
	static public function setTrimStringInputs($flag) {
		self::$trimStringInputs = $flag;
	}

	/**
	 * Returns the configuration of trimStringInputs.
	 *
	 * @return	boolean    The current value of self::$trimStringInputs
	 */
	static public function getTrimStringInputs() {
		return self::$trimStringInputs;
	}

	/**
	 * Sets the configuration of automaticFeaturesEnabled.
	 *
	 * @see getAutomaticFeaturesEnabled()
	 *
	 * @param	boolean	$flag
	 * @return	void
	 */
	static public function setAutomaticFeaturesEnabled($flag) {
		self::$automaticFeaturesEnabled = $flag;
	}

	/**
	 * Returns the configuration of automaticFeaturesEnabled.
	 *
	 * If the configuration is set to TRUE child nodes that match the criteria,
	 * are automatically grouped into arrays even if they doesn't have any
	 * siblings.
	 *
	 * @see _checkIfChildShouldBeACollection()
	 *
	 * @return	boolean    The current value of self::$automaticFeaturesEnabled
	 */
	static public function getAutomaticFeaturesEnabled() {
		return self::$automaticFeaturesEnabled;
	}
}