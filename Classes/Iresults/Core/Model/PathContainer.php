<?php

namespace Iresults\Core\Model;

use Iresults\Core\KVCInterface;
use Iresults\Core\Mutable;
use Iresults\Core\Mutable\Xml;
use Iresults\Core\Value;


/**
 * The iresults path container allows the storage, analysis and finding of
 * objects assigned to any kind of paths, including property key paths, tree
 * branches and similar.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Model
 */
class PathContainer extends \Iresults\Core\Model\PathAccess\AbstractContainer implements \Iresults\Core\Model\PathContainerInterface
{
    /**
     * Initializes a path container instance with the data from the given array.
     *
     * @param array $array The associative array|dictionary to read the data from
     * @return    \Iresults\Core\Model\PathContainerInterface
     *
     * @throws \InvalidArgumentException if the given value is not an object.
     */
    public function initWithArray($array)
    {
        foreach ($array as $key => $value) {
            $key = '' . $key;
            if (!is_object($value)) {
                throw new \InvalidArgumentException("The given value for key '$key' is not an object.", 1321542946);
            }
            $this->pathToObjectMap[$key] = $value;
            $this->hashToPathMap[spl_object_hash($value)] = $key;
        }

        return $this;
    }

    /**
     * Initializes a path container instance with the data from the given
     * mutable XML object.
     *
     * The XML file must have the following format:
     *
     *  <paths>
     *        <entry>
     *            <path>[1|2]??.??.??</path>
     *            <value>Wertzeichen</value>
     *        </entry>
     *        <entry>
     *            ...
     *        </entry>
     *    </paths>
     *
     * @param Xml     $mutable               The mutable object from which to read the data
     * @param boolean $throwOnDuplicatePaths If set to TRUE an exception will be thrown if a given path already exists in the pathToObject-map
     * @return    \Iresults\Core\Model\PathContainer
     * @throws \Iresults\Core\Model\PathAccess\Exception\DuplicatePath if $throwOnDuplicatePaths is TRUE and the path already exists
     */
    public function initWithMutableFromXml(Xml $mutable, $throwOnDuplicatePaths = false)
    {
        $array = $mutable->getObjectForKey('entry');
        foreach ($array as $entry) {
            /** @var Mutable $entry */
            $key = $entry->getObjectForKey('path');
            $value = $entry->getObjectForKey('value');

            if (!is_object($value)) {
                $value = new Value($value);
                // If the value doesn't have a path property save the path there.
            } elseif ($value instanceof KVCInterface && !$value->getObjectForKey('path')) {
                $value->setObjectForKey('path', $key);
            }
            if ($throwOnDuplicatePaths && isset($this->pathToObjectMap[$key])) {
                throw new PathAccess\Exception\DuplicatePath('Path "' . $key . '" already exists', 1363856583);
            }
            $this->pathToObjectMap[$key] = $value;
            $this->hashToPathMap[spl_object_hash($value)] = $key;
        }

        return $this;
    }

    /**
     * Factory method: Returns an empty path container instance.
     *
     * @return    \Iresults\Core\Model\PathContainerInterface
     */
    static public function container()
    {
        return new static();
    }

    /**
     * Factory method: Returns a path container instance with the data from the
     * given mutable.
     *
     * @param $mutable
     * @return PathContainerInterface
     * @internal param Mutable $object The mutable object from which the data will be read
     */
    static public function containerWithMutable($mutable)
    {
        if (is_a($mutable, '\Iresults\Core\Mutable\Xml')) {
            /** @var PathContainer $container */
            $container = static::container();
            $container->initWithMutableFromXml($mutable);

            return $container;
        }

        return self::containerWithArray($mutable);
    }

    /**
     * Factory method: Returns a path container instance with the data from the
     * given array.
     *
     * @param array $array The associative array|dictionary to read the data from
     * @return    \Iresults\Core\Model\PathContainerInterface
     */
    static public function containerWithArray($array)
    {
        $container = self::container();
        $container->initWithArray($array);

        return $container;
    }
}
