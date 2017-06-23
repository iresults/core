<?php

namespace Iresults\Core\Mutable;

use DOMDocument;
use Iresults\Core\Error;
use Iresults\Core\Iresults;
use Iresults\Core\Tools\StringTool;
use SimpleXMLElement;
use Traversable;


/**
 * The concrete implementation class for mutable objects that read data from a
 * XML file.
 *
 * @TODO          Make this work without TYPO3 CMS
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Mutable
 */
class Xml extends \Iresults\Core\Mutable
{
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
    static protected $useAtNotationForAttributes = false;

    /**
     * If set to TRUE a new \Iresults\Core\Mutable will be used for self closing
     * tags, if they don't own attributes.
     *
     * @var boolean
     */
    static protected $useEmptyMutableForSelfClosingTags = false;

    /**
     * The format in which the property keys will be converted to.
     * Defaults to \Iresults\Core\Tools\StringTool::FORMAT_UNDERSCORED.
     *
     * @var integer|StringTool::FORMAT
     */
    static protected $keyTransformFormat = StringTool::FORMAT_UNDERSCORED;

    /**
     * If set to TRUE nodes of type string will be trimmed.
     *
     * @var boolean
     */
    static protected $trimStringInputs = true;

    /**
     * If set to TRUE child nodes that match the criteria, are automatically
     * grouped into arrays even if they doesn't have any siblings.
     *
     * @var boolean
     */
    static protected $automaticFeaturesEnabled = false;


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* INITIALIZATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Initialize the instance with the contents of the given URL.
     *
     * @param string $url Path to the XML file
     * @return Xml
     * @throws Error if the XML could not be loaded
     */
    public function initWithContentsOfUrl($url)
    {
        $xmlString = '';

        /*
         * Check if the environment is UTF-8
         */
        $xmlDoc = simplexml_load_file($url);

        if (!$xmlDoc) {
            $xmlError = libxml_get_last_error();
            if ($xmlString) {
                $xmlDebugString = '';
                $xmlString = str_replace(["\r", "\r\n", "\n"], PHP_EOL, $xmlString);
                $xmlStringParts = explode(PHP_EOL, $xmlString);
                foreach ($xmlStringParts as $lineNumber => $xmlLine) {
                    $xmlDebugString .= '#' . ($lineNumber + 1) . ": \t $xmlLine" . PHP_EOL;
                }
                Iresults::pd($xmlDebugString);
            }
            if ($xmlError === false) {
                $xmlError = "Couldn't detect the XML error (This may be because of a timeout or a firewall)";
                throw Error::errorWithMessageCodeAndUserInfo($xmlError, 1337692752, [$xmlError]);
            }
            throw Error::errorWithMessageCodeAndUserInfo($xmlError->message, $xmlError->code, [$xmlError]);
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
     * @return    string
     */
    public function asXML()
    {
        $xmlString = '';

        $encodingString = ' encoding="UTF-8"';

        foreach ($this as $name => $data) {
            $currentDataString = $data;
            if (is_object($data) && $data instanceof \Iresults\Core\Mutable\Xml) {
                $currentDataString = $data->asXML();
            } elseif (is_array($data)) {
                $currentDataString = '';
                foreach ($data as $key => $value) {
                    if (is_object($value) && $value instanceof \Iresults\Core\Mutable\Xml) {
                        $currentDataString .= $value->asXML();
                    } else {
                        $currentDataString .= '<' . $key . '>' . PHP_EOL . $value . PHP_EOL . '</' . $key . '>';
                    }
                }
            }
            $currentDataString = str_replace(
                [
                    '<?xml version=\'1.0\'' . $encodingString . '?>',
                    '<?xml version="1.0"' . $encodingString . '?>',
                    '<iresults_mutable_xml_root>',
                    '</iresults_mutable_xml_root>',
                ],
                '',
                $currentDataString
            );
            $xmlString .= '<' . $name . '>' . $currentDataString . '</' . $name . '>' . PHP_EOL;
        }

        // Format the XML string
        $xmlString = trim($xmlString);
        if (class_exists('DOMDocument')) {
            $xmlString = '<?xml version=\'1.0\'' . $encodingString . '?><iresults_mutable_xml_root>' . $xmlString . '</iresults_mutable_xml_root>';
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xmlString);
            $xmlString = $dom->saveXML();
        } else {
            $xmlObject = simplexml_load_string(
                '<?xml version=\'1.0\'' . $encodingString . '?><iresults_mutable_xml_root>' . $xmlString . '</iresults_mutable_xml_root>'
            );
            if (is_object($xmlObject)) {
                $xmlString = $xmlObject->asXML();
            }
        }
        $xmlString = str_replace(
            [
                '<?xml version=\'1.0\'' . $encodingString . '?>',
                '<?xml version="1.0"' . $encodingString . '?>',
                '<iresults_mutable_xml_root>',
                '</iresults_mutable_xml_root>',
            ],
            '',
            $xmlString
        );

        return $xmlString;
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* XML TRAVERSING        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Sets the data from the given XML node as the properties of the mutable
     * object.
     *
     * @param SimpleXMLElement       $data       The XML node to process
     * @param \Iresults\Core\Mutable $object     The object whose properties to set
     * @param string                 $objectName The node name of the given object
     * @return    void
     */
    protected function _addXmlDataToObject($data, \Iresults\Core\Mutable $object, $objectName = '')
    {
        $automaticFeaturesEnabledLocal = self::$automaticFeaturesEnabled;

        /*
         * Loop through all children of the data node.
         */
        foreach ($data as $key => $currentChild) {
            $key = $this->_prepareKeyOfNode($key);

            /*
             * Check if the key is already set.
             */
            if ($object->getObjectForKey($key)) {
                $oldChild = $object->getObjectForKey($key);
                if (!is_array($oldChild) || $oldChild instanceof Traversable) {
                    $oldChild = [$oldChild];
                }
                $oldChild[] = $this->_getRepresentationForNode($currentChild);
                $object->setObjectForKey($key, $oldChild);
                /*
                 * Check if the automatic features should be applied.
                 */
            } elseif ($objectName && $automaticFeaturesEnabledLocal &&
                $this->_checkIfNamesIndicateThatTheChildIsACollection($objectName, $key)
            ) {
                $collection = [];
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
     * @param SimpleXMLElement       $data   The XML node to process
     * @param \Iresults\Core\Mutable $object The object whose property to set
     * @return    void
     */
    protected function _addXmlAttributesToObject($data, \Iresults\Core\Mutable $object)
    {
        if (!is_object($data)) {
            return;
        }

        $attributesXml = $data->attributes();
        if ($attributesXml->count()) {
            $attributes = [];
            foreach ($attributesXml as $key => $attribute) {
                $key = $this->_prepareKeyOfNode($key);
                if (self::$useAtNotationForAttributes) { // Set the value directly
                    $object->setObjectForKey('@' . $key, $this->_getRepresentationForNode($attribute));
                } else { // Store it in the attributes array
                    $attributes[$key] = $this->_getRepresentationForNode($attribute);
                }
            }
            if (!self::$useAtNotationForAttributes) // Set the attributes array as property with the key 'attributes'
            {
                $object->setObjectForKey(self::ATTRIBUTES_PROPERTY_KEY, $attributes);
            }
        }


    }

    /**
     * Returns a representation of the given XML node which may be set as the
     * property of the mutable object.
     *
     * @param SimpleXMLElement $node The node to get a representation of
     * @return    mixed
     */
    protected function _getRepresentationForNode($node)
    {
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
        } else /*
		 * Check if the node is a scalar but has attributes attached.
		 */ {
            if ($node->count() === 0 && $node->attributes()->count() != 0) {
                $newNodeObject = $this->_createSubObjectForNode($node);
                $this->_addXmlAttributesToObject($node, $newNodeObject);
                $newNodeObject->setObjectForKey('value', $this->_createEndPointOfNode($node));
                $node = $newNodeObject;
            } else /*
		 * It is an other node -> create a new mutable for it.
		 */ {
                if ($node->count() > 0) {
                    $newNodeObject = $this->_createSubObjectForNode($node);
                    $nodeName = $this->_prepareKeyOfNode($node->getName());
                    $this->_addXmlDataToObject($node, $newNodeObject, $nodeName);
                    $this->_addXmlAttributesToObject($node, $newNodeObject);
                    $node = $newNodeObject;
                } else {
                    $this->pd("Couldn't handle node name '{$node->getName()}'");
                }
            }
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
     * @param string $key The key to prepare
     * @return string
     */
    protected function _prepareKeyOfNode($key)
    {
        $key = str_replace(['/', ',', '|', '\\'], '_', $key);
        if (self::$keyTransformFormat !== StringTool::FORMAT_KEEP) {
            return StringTool::transformStringToFormat($key, self::$keyTransformFormat);
        }

        return lcfirst($key);
    }

    /**
     * Prepares the XML string before it is passed through simplexml_load_string().
     *
     * @param string $xmlString
     * @return    string
     */
    protected function _prepareXmlString($xmlString)
    {
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
     * @param SimpleXMLElement $node The node
     * @return    mixed
     */
    protected function _createEndPointOfNode($node)
    {
        if ("$node" === '0') {
            $node = 0.0;
        } elseif (!("$node")) { // Handle self closing tags without arguments.
            if (self::$useEmptyMutableForSelfClosingTags) {
                $node = $this->_createSubObjectForNode($node);
            } else {
                $node = true;
            }
        } elseif (substr("$node", 0, 1) !== '0' && is_numeric("$node")) {
            $node = (float)$node;
        } elseif (self::$trimStringInputs) {
            $node = trim($node);
        } else {
            $node = "$node";
        }

        return $node;
    }

    /**
     * Creates and returns a new sub object
     *
     * @param SimpleXMLElement $node The corresponding XML node is passed for handling in subclasses
     *
     * @return    \Iresults\Core\Mutable\Xml|object
     */
    protected function _createSubObjectForNode($node)
    {
        return new static();
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
     * @param \Iresults\Core\Mutable $parent     The parent node
     * @param mixed                  $child      The child node
     * @param string                 $parentName The node name of the parent
     * @param string                 $childName  The node name of the child
     * @return    boolean    Returns TRUE if the child should be an array
     */
    protected function _checkIfChildShouldBeACollection($parent, $child, $parentName, $childName)
    {
        /*
         * Check if the key is already set.
         */
        if ($parent->getObjectForKey($childName)) {
            return true;
            /*
             * Check if the automatic features should be applied.
             */
        } elseif (self::$automaticFeaturesEnabled && $this->_checkIfNamesIndicateThatTheChildIsACollection(
                $parentName,
                $childName
            )
        ) {
            return true;
        }

        return false;
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
     * @param string $parentName The node name of the parent
     * @param string $childName  The node name of the child
     * @return    boolean    Returns TRUE if the child should be an array
     */
    protected function _checkIfNamesIndicateThatTheChildIsACollection($parentName, $childName)
    {
        if (!$parentName || !$childName) {
            return false;
        }

        /*
         * Test 1:
         */
        if ($parentName === $childName . 's') {
            return true;
        }

        /*
         * Test 2:
         */
        if (substr($parentName, -3) === 'ies' && substr($childName, -1) === 'y' &&
            substr($parentName, 0, -3) === substr($childName, 0, -1)
        ) {
            return true;
        }

        return false;
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* OVERRIDE THE GETTER   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns a properties data
     *
     * @param string $name
     * @return    mixed
     */
    public function __get($name)
    {
        $value = parent::__get($name);
        if (is_null($value) && preg_match('!^[^A-Z].*[A-Z]!', $name)) {
            $name = StringTool::camelCaseToLowerCaseUnderscored($name);
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
     * @param boolean $flag
     * @return    void
     */
    static public function setUseAtNotation($flag)
    {
        self::$useAtNotationForAttributes = $flag;
    }

    /**
     * Returns the configuration of useAtNotationForAttributes.
     *
     * @return    boolean    The current value of self::$useAtNotationForAttributes
     */
    static public function getUseAtNotation()
    {
        return self::$useAtNotationForAttributes;
    }

    /**
     * Sets the configuration of keyTransformFormat.
     *
     * @param integer|\Iresults\Core\Tools\StringTool::FORMAT $format The format to transform to
     *
     * @return    void
     */
    static public function setKeyTransformFormat($format)
    {
        self::$keyTransformFormat = $format;
    }

    /**
     * Returns the configuration of keyTransformFormat.
     *
     * @return    integer|StringTool::FORMAT The current configuration
     */
    static public function getKeyTransformFormat()
    {
        return self::$keyTransformFormat;
    }

    /**
     * Sets the configuration of useEmptyMutableForSelfClosingTags.
     *
     * @param boolean $flag
     * @return    void
     */
    static public function setUseEmptyMutableForSelfClosingTags($flag)
    {
        self::$useEmptyMutableForSelfClosingTags = $flag;
    }

    /**
     * Returns the configuration of useEmptyMutableForSelfClosingTags.
     *
     * @return    boolean    The current value of self::$useEmptyMutableForSelfClosingTags
     */
    static public function getUseEmptyMutableForSelfClosingTags()
    {
        return self::$useEmptyMutableForSelfClosingTags;
    }

    /**
     * Sets the configuration of trimStringInputs.
     *
     * @param boolean $flag
     * @return    void
     */
    static public function setTrimStringInputs($flag)
    {
        self::$trimStringInputs = $flag;
    }

    /**
     * Returns the configuration of trimStringInputs.
     *
     * @return    boolean    The current value of self::$trimStringInputs
     */
    static public function getTrimStringInputs()
    {
        return self::$trimStringInputs;
    }

    /**
     * Sets the configuration of automaticFeaturesEnabled.
     *
     * @see getAutomaticFeaturesEnabled()
     *
     * @param boolean $flag
     * @return    void
     */
    static public function setAutomaticFeaturesEnabled($flag)
    {
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
     * @return    boolean    The current value of self::$automaticFeaturesEnabled
     */
    static public function getAutomaticFeaturesEnabled()
    {
        return self::$automaticFeaturesEnabled;
    }

    /**
     * Returns the configuration of 'noUTF8'.
     *
     * If the configuration is set to TRUE the node contents will be converted
     * to latin1 (ISO-8859-1).
     * The node names will be converted to latin1 anyway.
     *
     * @return    boolean    The current value of self::$noUTF8
     */
    static public function getNoUTF8()
    {
        return false;
    }
}
