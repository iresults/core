<?php

namespace Iresults\Core\Model\PathAccess;

use Iresults\Core\Helpers\ObjectHelper;
use Iresults\Core\Iresults;
use Iresults\Core\Model\PathAccess\Exception\DuplicateEntry;


/**
 * The iresults path access abstract container provides common functionality for
 * class implementing the \Iresults\Core\Model\PathAccessInterface.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Model_PathAccess
 */
abstract class AbstractContainer extends \Iresults\Core\Model implements \Iresults\Core\Model\PathAccessInterface
{
    /**
     * Separator for Number Range Expressions
     */
    const SEPARATOR_NUMBER_RANGE_EXPRESSION = '#';

    /**
     * Pattern for Number Range Expressions
     */
    const PATTERN_NUMBER_RANGE_EXPRESSION = '!\d*#\d*!';

    /**
     * Default regular expression delimiter
     */
    const DELIMITER = '!';

    /**
     * The dictionary holding the paths as keys and objects as values.
     *
     * @var array<object>
     */
    protected $pathToObjectMap = [];

    /**
     * The dictionary holding the object hashes and the paths to the objects.
     *
     * @var array<string>
     */
    protected $hashToPathMap = [];

    /**
     * The path separator that separates the individual parts of the path. This defaults to the dot character ('.').
     *
     * @var string
     */
    protected $pathSeparator = '.';

    /**
     * The pattern delimiter that will be used as delimiter for the search expressions. This defaults to an exclamation mark ('!').
     *
     * @var string
     */
    protected $patternDelimiter = '!';


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESS BY PATH        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the object at the given path.
     *
     * Example:
     *
     *   1.  0. 1      =>   obj1
     *   1.  2. 0      =>  [obj2]
     *   4.  5. 1      =>   obj3
     *   5. 12. 3      =>   obj4
     *   5. 14. 0. 3   =>   obj5
     *
     * The path 1.2.0 would return the object in square brackets.
     *
     * @param string $path The path to the object
     * @return    object    The object at the given path, or NULL if none exists
     */
    public function getObjectAtPath($path)
    {
        if (isset($this->pathToObjectMap[$path])) {
            return $this->pathToObjectMap[$path];
        }

        return null;
    }

    /**
     * Sets the object at the given path.
     *
     * Example:
     *
     *   1.  0. 1      =>   obj1
     *   1.  2. 0      =>  [obj2]
     *   4.  5. 1      =>   obj3
     *   5. 12. 3      =>   obj4
     *   5. 14. 0. 3   =>   obj5
     *
     * The path 1.2.0 would point to the object in square brackets.
     *
     * @param string $path   The path to set
     * @param object $object The new object
     * @throws \InvalidArgumentException if the given value is not an object.
     * @throws Exception\DuplicateEntry
     * @return    void
     *
     */
    public function setObjectAtPath($path, $object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException(
                'The given value is not an object, but of type ' . gettype($object) . '.', 1321375068
            );
        }
        if ($this->containsObject($object)) {
            Iresults::pd($object);
            throw new DuplicateEntry(
                'The object of class ' . get_class($object) . ' already exists in the tree.',
                1321362340
            );
        }

        $objectHash = ObjectHelper::createIdentfierForObject($object);
        $this->pathToObjectMap['' . $path] = $object;
        $this->hashToPathMap[$objectHash] = '' . $path;
    }

    /**
     * Returns if an object exists at the given path.
     *
     * Example:
     *
     *   1.  0. 1      =>   obj1
     *   1.  2. 0      =>  [obj2]
     *   4.  5. 1      =>   obj3
     *   5. 12. 3      =>   obj4
     *   5. 14. 0. 3   =>   obj5
     *
     * The path 1.2.0 would return TRUE.
     *
     * @param string $path The path to the object
     * @return    boolean    TRUE if an object exists at the given path, otherwise FALSE
     */
    public function hasObjectAtPath($path)
    {
        if ($path === 'Root') {
            return true;
        }
        if (isset($this->pathToObjectMap[$path])) {
            return true;
        }

        return false;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESS BY OBJECT      MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the path to the given object.
     *
     * @param object $object A object in the tree
     * @return    string    The path to the given object
     *
     * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the object doesn't exist in the tree.
     */
    public function getPathOfObject($object)
    {
        $objectHash = \Iresults\Core\Helpers\ObjectHelper::createIdentfierForObject($object);
        if (!isset($this->hashToPathMap[$objectHash])) {
            $objectClass = 'NULL';
            if ($object) {
                $objectClass = get_class($object);
            }
            throw new \Iresults\Core\Model\PathAccess\Exception\EntryNotFound(
                "The object of class $objectClass with hash '$objectHash' doesn't exist in the tree.", 1321352579
            );
        }

        return $this->hashToPathMap[$objectHash];
    }

    /**
     * Returns if the object exists in the tree.
     *
     * @param object $object The object to search in the tree
     * @return    boolean    TRUE if the object is in the tree, otherwise FALSE
     */
    public function containsObject($object)
    {
        $objectHash = \Iresults\Core\Helpers\ObjectHelper::createIdentfierForObject($object);
        if (isset($this->hashToPathMap[$objectHash])) {
            return true;
        }

        return false;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FINDING PATHS AND OBJECTS    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns all the paths that match the given pattern.
     *
     * The pattern may contain asterisk characters ('*') to match zero or more
     * characters, or a question mark ('?') matching a single character in the
     * path.
     *
     * If $isParsed is set to TRUE the given pattern will be treated as regular
     * expression and will not be parsed.
     *
     * @param string  $pattern  A path pattern
     * @param boolean $isParsed Indicates if the given pattern is already parsed
     * @return    array<string>    The matching paths or an empty array if none was found
     */
    public function findAllPathsMatchingPattern($pattern, $isParsed = false)
    {
        // Change the pattern if a pattern is given that looks for root paths.
        if ($pattern === 'Root' || $pattern === $this->pathSeparator . '*' || $pattern === 'Root' . $this->pathSeparator . '*') {
            $pattern = '*';
        }

        // Prepare the pattern
        if (!$isParsed) {
            $pattern = self::wildcardStringToRegularExpression($pattern, $this->patternDelimiter);
        }

        // Get all the paths and make sure they are strings.
        $paths = array_keys($this->pathToObjectMap);
        array_walk(
            $paths,
            function (&$element) {
                $element = '' . $element;
            }
        );
        $foundPaths = preg_grep($pattern, $paths);

        return $foundPaths;
    }

    /**
     * Returns all the paths that begin with the given prefix
     *
     * @param string $prefix The prefix to search for
     * @return    array<string>    The matching paths or an empty array if none was found
     */
    public function findAllPathsWithPrefix($prefix)
    {
        $prefixLength = strlen($prefix);

        // Get all the paths and make sure they are strings.
        $paths = array_keys($this->pathToObjectMap);
        array_walk(
            $paths,
            function (&$element) {
                $element = '' . $element;
            }
        );

        return array_filter(
            $paths,
            function ($currentPath) use ($prefix, $prefixLength) {
                return substr($currentPath, 0, $prefixLength) === $prefix;
            }
        );
    }

    /**
     * Returns the path that best matches the given pattern.
     *
     * The pattern may contain asterisk characters ('*') to match zero or more
     * characters, or a question mark ('?') matching a single character in the
     * path.
     *
     * If $isParsed is set to TRUE the given pattern will be treated as regular
     * expression and will not be parsed.
     *
     * @param string  $pattern  A path pattern
     * @param boolean $isParsed Indicates if the given pattern is already parsed
     * @return    string    The best matching path
     */
    public function findPathMatchingPattern($pattern, $isParsed = false)
    {
        return reset($this->findAllPathsMatchingPattern($pattern, $isParsed));
    }

    /**
     * Returns all objects whose path match the given pattern.
     *
     * The pattern may contain asterisk characters ('*') to match zero or more
     * characters, or a question mark ('?') matching a single character in the
     * path.
     *
     * If $isParsed is set to TRUE the given pattern will be treated as regular
     * expression and will not be parsed.
     *
     * @param string  $pattern  A path pattern
     * @param boolean $isParsed Indicates if the given pattern is already parsed
     * @return    array<object>    The matching objects or an empty array if none was found
     */
    public function findAllObjectsWithPathsMatchingPattern($pattern, $isParsed = false)
    {
        $foundObjects = [];
        $foundPaths = $this->findAllPathsMatchingPattern($pattern);
        foreach ($foundPaths as $path) {
            $foundObjects['' . $path] = $this->pathToObjectMap['' . $path];
            #$foundObjects[] = $this->getObjectAtPath($path);
        }

        return $foundObjects;
    }

    /**
     * Returns the object whose path best matches the given pattern.
     *
     * The pattern may contain asterisk characters ('*') to match zero or more
     * characters, or a question mark ('?') matching a single character in the
     * path.
     *
     * If $isParsed is set to TRUE the given pattern will be treated as regular
     * expression and will not be parsed.
     *
     * @param string  $pattern  A path pattern
     * @param boolean $isParsed Indicates if the given pattern is already parsed
     * @return    object    The object with the best matching path
     */
    public function findObjectWithPathMatchingPattern($pattern, $isParsed = false)
    {
        return reset($this->findAllObjectsWithPathsMatchingPattern($pattern, $isParsed));
    }

    /**
     * Returns the paths that are most similar to the given path.
     *
     * Example:
     *
     *   1.  0. 1      =>   obj1
     *   1.  2. 0      =>   obj2
     *   4.  5. 1      =>   obj3
     *   5. 11. 3      =>   obj4
     *   5. 14. 0. 3   =>   obj5
     *
     * The path 1.2.0 would return 1.2.0.
     * The path 4.5.3 would return 4.5.1.
     * The path 5.12.4 would return 5.11.3.
     *
     * @param string $path The path to find the similar paths to
     * @return    array<string>    The similar paths or an empty array if none are found
     */
    public function findPathMostSimilarToPath($path)
    {
        $matchingPaths = [];
        $matchingPathsParts = [];
        $currentPath = $path;
        $pathSeparatorL = $this->pathSeparator;
        $levels = substr_count($path, $pathSeparatorL) + 1;
        $comparisonBaseLevel = $levels;

        // If a object for the given path exists, just return the path
        if ($this->hasObjectAtPath($path)) {
            return $path;
        }

        // Find the paths at the first matching level.
        while (empty($matchingPaths) && $currentPath !== '') {
            $matchingPaths = $this->findAllPathsMatchingPattern($currentPath . $pathSeparatorL . '*');
            $currentPath = substr($currentPath, 0, (int)strrpos($currentPath, $pathSeparatorL));
            $comparisonBaseLevel--;
        }
        if (empty($matchingPaths)) {
            $matchingPaths = array_keys($this->pathToObjectMap);
            #throw new Exception("Error while trying to find a path similar to path '$path'.", 1321536998);
        }
        \Iresults\Core\Iresults::pd($matchingPaths);

        /*
         * Create the path parts of the relevant paths.
         */
        $inputPathParts = explode($pathSeparatorL, $path);
        foreach ($matchingPaths as $matchingPath) {
            $matchingPathsParts[$matchingPath] = explode($pathSeparatorL, $matchingPath);
        }

        /*
         * Create the path hashes of the path parts from the comparison base level.
         */
        $matchingPathsHashes = [];
        $hashElements = $levels - $comparisonBaseLevel;
        foreach ($matchingPaths as $matchingPath) {
            $pathPartsFromBaseLevel = array_slice(explode($pathSeparatorL, $matchingPath), $comparisonBaseLevel);
            $matchingPathsHashes[$matchingPath] = $this->_createPathHashOfPathPartsWithDepth(
                $pathPartsFromBaseLevel,
                $hashElements
            );
        }
        // Add the input path
        $pathPartsFromBaseLevel = array_slice($inputPathParts, $comparisonBaseLevel);
        $inputPathsHashes = $this->_createPathHashOfPathPartsWithDepth($pathPartsFromBaseLevel, $hashElements);
        $matchingPathsHashes[$path] = $inputPathsHashes;

        // Sort the paths by there hash
        asort($matchingPathsHashes, SORT_NUMERIC);
        $bestPath = $this->_comparePathHashesToInputPathHash($matchingPathsHashes, $inputPathsHashes);

        return $bestPath;
    }

    /**
     * Returns the objects whose paths are most similar to the given path.
     *
     * Example:
     *
     *   1.  0. 1      =>   obj1
     *   1.  2. 0      =>   obj2
     *   4.  5. 1      =>   obj3
     *   5. 11. 3      =>   obj4
     *   5. 14. 0. 3   =>   obj5
     *
     * The path 1.2.0 would return obj2.
     * The path 4.5.3 would return obj3.
     * The path 5.12.4 would return obj4.
     *
     * @param string $path The path to find the similar paths to
     * @return    object    The object with matching with similar paths or an empty array if none are found
     */
    public function findObjectMostSimilarToPath($path)
    {
        $mostSimilarPath = $this->findPathMostSimilarToPath($path);

        return $this->getObjectAtPath($mostSimilarPath);
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MANIPULATING PATHS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the path to the parent node of the node at the given path.
     *
     * @param string $path A path
     * @return    string    The path to the parent
     */
    public function getParentPathOfPath($path)
    {
        $lastDotPosition = (int)strrpos($path, $this->pathSeparator);

        return substr($path, 0, $lastDotPosition);
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FINDING PATTERNS AND OBJECTS WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns all path-patterns that match the given path.
     *
     * This method works analog to findAllPathsMatchingPattern() method, but
     * treats the containers paths as patterns and compares them against the
     * given path.
     *
     * @param string $path The path to find a matching pattern
     * @return    array<string>    The matching patterns or an empty array if none was found
     */
    public function findAllPatternsMatchingPath($path)
    {
        $foundPatterns = [];
        $patternDelimiterL = $this->patternDelimiter;

        // Get all the paths and make sure they are strings.
        $patterns = array_keys($this->pathToObjectMap);
        foreach ($patterns as $pattern) {
            $originalPattern = $pattern;

            // Prepare the pattern if not already a regex
            if (substr($pattern, 0, 1) !== $patternDelimiterL) {
                $pattern = self::wildcardStringToRegularExpression($pattern, $patternDelimiterL);
            }
            if (preg_match($pattern, $path)) {
                $foundPatterns[] = $originalPattern;
            }
        }

        return $foundPatterns;
    }

    /**
     * Returns all objects whose pattern match the given path.
     *
     * This method works analog to findAllObjectsWithPathsMatchingPattern()
     * method, but treats the containers paths as patterns and compares them
     * against the given path.
     *
     * @param string $path The path to find a matching pattern
     * @return    array<object>    The objects with matching paths or an empty array if none was found
     */
    public function findAllObjectsWithPatternsMatchingPath($path)
    {
        $foundObjects = [];
        $foundPatterns = $this->findAllPatternsMatchingPath($path);
        foreach ($foundPatterns as $pattern) {
            $foundObjects[$pattern] = $this->pathToObjectMap[$pattern];
            #$foundObjects[] = $this->getObjectAtPath($path);
        }

        return $foundObjects;
    }

    /**
     * Returns the objects that have the given value for the property with the
     * given key.
     *
     * Property key paths may also be passed to compare those values.
     *
     * @param string $propertyKey   The property key to look for
     * @param mixed  $propertyValue The property value to look for
     * @return    array<object>    The objects with matching properties or an empty array if none are found
     */
    public function findObjectsWithProperty($propertyKey, $propertyValue)
    {
        return $this->_getObjectCollectionSearchHelper()->findObjectsWithPropertyInCollection(
            $propertyKey,
            $propertyValue,
            $this->pathToObjectMap
        );
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* CONFIGURATION                      MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the path separator.
     *
     * @return    string
     */
    public function getPathSeparator()
    {
        return $this->pathSeparator;
    }

    /**
     * Sets the path separator.
     *
     * @param string $newValue The new path seperator to use
     * @return    void
     */
    public function setPathSeparator($newValue)
    {
        $this->pathSeparator = $newValue;
    }

    /**
     * Returns the pattern delimiter.
     *
     * @return    string
     */
    public function getPatternDelimiter()
    {
        return $this->patternDelimiter;
    }

    /**
     * Sets the pattern delimiter.
     *
     * @param string $newValue The new pattern delimiter to use
     * @return    void
     */
    public function setPatternDelimiter($newValue)
    {
        $this->patternDelimiter = $newValue;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* HELPER METHODS    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Searches the tree's branches for objects whose paths matching the given
     * pattern.
     *
     * A pattern contains astrisks for variable path parts and may look like one
     * of the following examples:
     *    2.0.*
     *    1.*.0
     *    2.*.*.1
     *    *.1.*.1
     *
     * @param string $pattern A path pattern to search for
     * @return    array<object> An array of objects whose path match the given pattern
     */
    //protected function _searchObjectsWithPathLikePattern($pattern) {
    //}

    /**
     * Searches the tree's branches for paths matching the given pattern.
     *
     * A pattern contains astrisks for variable path parts and may look like one
     * of the following examples:
     *    2.0.*
     *    1.*.0
     *    2.*.*.1
     *    *.1.*.1
     *
     * @param string $pattern A path pattern to search for
     * @return    array<string> An array of the tree's paths matching the given pattern
     */
    //protected function _searchPathsLikePattern($pattern) {
    //}

    /**
     * Returns the raw path-to-object map.
     *
     * @return    array<object>    The path-to-object map
     */
    public function getRawPathToObjectMap()
    {
        return $this->pathToObjectMap;
    }

    /**
     * Returns the raw hash-to-path map.
     *
     * @return    array<object>    The hash-to-path map
     */
    public function getRawHashToPathMap()
    {
        return $this->hashToPathMap;
    }

    /**
     * Returns the path parts at the given level.
     *
     * @param array   $paths Reference to the found paths
     * @param integer $level The level to get
     * @return    array<string>    An array of path parts
     */
    protected function _getPathPartsOfPathsAtLevel(&$paths, $level)
    {
        $comparisonLevelPathParts = [];
        $pathSeparatorL = $this->pathSeparator;
        foreach ($paths as $path) {
            $pathParts = explode($pathSeparatorL, $path);
            $comparisonLevelPathParts[$path] = $pathParts[$level];
        }
        \Iresults\Core\Iresults::pd($paths, $comparisonLevelPathParts, $level);

        return $comparisonLevelPathParts;
    }

    /**
     * Compares the found paths' hashes to the input's path hash and returns the
     * path that fitts the best.
     *
     * @param array   $pathHashes    Reference to the path hashes array
     * @param integer $inputPathHash The hash of the input path
     * @return    string    The path that matches best
     */
    protected function _comparePathHashesToInputPathHash(&$pathHashes, $inputPathHash)
    {
        // Get the position of the input path hash inside the array of hashes
        $inputPathHashIndex = array_search($inputPathHash, array_values($pathHashes));
        $useIndex = -1;

        $differenzToElementBefore = 0;
        $differenzToElementAfter = 0;

        // Check if there is an element before
        if ($inputPathHashIndex === 0) { // Get the object before the input path part
            $useIndex = 1; // Use index of second element
        } else {
            $differenzToElementBefore = 1.0 * $inputPathHash - $pathHashes[$inputPathHashIndex - 1];
        }

        // Check if $useIndex is not set and if there is an element after
        if ($useIndex === -1 && $inputPathHashIndex === (count(
                    $pathHashes
                ) - 1)
        ) { // Get the object before the input path part
            $useIndex = count($pathHashes) - 2; // Use index of last element
        } else {
            $differenzToElementAfter = $pathHashes[$inputPathHashIndex + 1] - 1.0 * $inputPathHash;
        }

        // If $useIndex is still not set compare the differenzes
        if ($useIndex === -1) {
            if (!$differenzToElementBefore || !$differenzToElementAfter) {
                trigger_error("The input path part '$inputPathHash' is found twice in the path parts array.");
            }

            if ($differenzToElementBefore < $differenzToElementAfter) {
                $useIndex = $inputPathHashIndex - 1;
            } else {
                $useIndex = $inputPathHashIndex + 1;
            }
        }

        $paths = array_keys($pathHashes);

        return $paths[$useIndex];
    }

    /**
     * Compares the array of path parts with the path part of the input.
     *
     * The path parts only consist of the parts of paths at the comparison level.
     *
     * Example 1:
     *
     *  Input path part: 56
     *  Path part:       array( '4' , '56' , '60' , '72' )
     *  The index of '60' (2) would be returned, because of the lower difference
     *  between the neighbours of '56'.
     *
     * Example 2:
     *
     *  Input path part: 4
     *  Path part:       array( '4' , '56' , '60' , '72' )
     *  The index of '56' (1) would be returned, because the input is the first
     *  element.
     *
     * @param array  $pathParts     Reference to the array of path parts
     * @param string $inputPathPart The input path part to search for
     * @return    integer    The index of the best match or -1 on error
     */
    protected function _compareComparisonLevelPathPartsToInputPathPart(&$pathParts, $inputPathPart)
    {
        // Get the position of the input path part
        $inputPathPartIndex = array_search($inputPathPart, $pathParts);

        if (is_numeric($inputPathPart)) {

            if ($inputPathPartIndex === 0) { // Get the object before the input path part
                return 1; // Return index of last element
            } else {
                $differenceToPartBefore = 1.0 * $inputPathPart - $pathParts[$inputPathPartIndex - 1];
            }

            if ($inputPathPartIndex === (count($pathParts) - 1)) { // Get the object before the input path part
                return count($pathParts) - 2; // Return index of last element
            } else {
                $differenceToPartAfter = $pathParts[$inputPathPartIndex + 1] - 1.0 * $inputPathPart;
            }

            if (!$differenceToPartBefore || !$differenceToPartAfter) {
                trigger_error("The input path part '$inputPathPart' is found twice in the path parts array.");
            }

            if ($differenceToPartBefore < $differenceToPartAfter) {
                return $inputPathPartIndex - 1;
            } else {
                return $inputPathPartIndex + 1;
            }
        } else {
            $indexOfPartBefore = $inputPathPartIndex - 1;
            if ($indexOfPartBefore > 0) {
                return $indexOfPartBefore;
            }

            return 0;
        }
    }

    /**
     * Creates a weighted sum of the given path parts.
     *
     * @param array   $pathParts Reference to the path parts array
     * @param integer $depth     The number of elements to sum
     * @return    integer    A weighted sum of the array elements values
     */
    protected function _createPathHashOfPathPartsWithDepth(&$pathParts, $depth)
    {
        $pathHash = 0;
        $depth += 1;
        $j = 0;
        for ($i = $depth; $i > 0; $i--) {
            if (!isset($pathParts[$j])) {
                break;
            }
            $pathHash += $pathParts[$j] * $i;
            $j++;
        }

        return $pathHash;
    }

    /**
     * Invoked by array_walk inside findAllPathsMatchingPattern() to cast the
     * paths into strings.
     *
     * @param mixed $element The path to cast into a string
     * @param mixed $key     The array key/index of the element
     * @return    void
     */
    static public function arrayKeysToStringsInArrayWalk(&$element, $key)
    {
        $element = '' . $element;
    }

    /**
     * Returns the given pattern with wildcards transformed into regular
     * expression syntax.
     *
     * Example:
     *
     *  [1|2]??.*.[0|1]?
     *
     *  will be parsed to
     *
     *  !^[1|2][\w|_|\-][\w|_|\-]\.[\w|_|\-]*\.[0|1][\w|_|\-]$!
     *
     * @param string $pattern   The pattern with wildcards
     * @param string $delimiter !' The pattern delimiter
     * @param string $options   The regular expression options that will be applied
     * @return    string    The transformed regular expression
     */
    static public function wildcardStringToRegularExpression($pattern, $delimiter = self::DELIMITER, $options = '')
    {
        // Prepare the pattern if not already a regex
        if (substr($pattern, 0, 1) !== $delimiter) {
            $pattern = str_replace('.', '\\.', $pattern);
            $pattern = str_replace('*', '[\\w|_|\-]*', $pattern);
            $pattern = str_replace('?', '[\\w|_|\-]', $pattern);

            // Check for Number Range Expressions
            if (strpos($pattern, self::SEPARATOR_NUMBER_RANGE_EXPRESSION) !== false) {
                $pattern = self::replaceNumberRangeToRegularExpression($pattern);
            }
            $pattern = $delimiter . '^' . $pattern . '$' . $delimiter;
        }

        $pattern .= $options;

        return $pattern;
    }

    /**
     * Returns the Number Range Expression in the pattern.
     *
     * Currently only integers are supported.
     *
     * Example:
     *
     *  91#107
     *
     *  will be parsed to
     *
     *  (91|92|93|94|95|96|97|98|99|100|101|102|103|104|105|106|107)
     *
     * @param string $pattern            The pattern in which to replace the Number Range Expressions
     * @param string $numberRangePattern The pattern to find Number Range Expressions
     * @return    string
     */
    static public function replaceNumberRangeToRegularExpression(
        $pattern,
        $numberRangePattern = self::PATTERN_NUMBER_RANGE_EXPRESSION
    ) {
        $foundNumberRange = [];
        if (preg_match_all($numberRangePattern, $pattern, $foundNumberRange)) {
            $foundNumberRange = $foundNumberRange[0];
            foreach ($foundNumberRange as $numberRange) {
                $numberRangeExpression = self::numberRangeToRegularExpression($numberRange);
                $pattern = preg_replace(
                    self::DELIMITER . $numberRange . self::DELIMITER,
                    $numberRangeExpression,
                    $pattern,
                    1
                );
            }
        }

        return $pattern;
    }

    /**
     * Returns the regular expression for the given Number Range Expression.
     *
     * @param string $numberRange The Number Range
     * @return    string
     */
    static public function numberRangeToRegularExpression($numberRange)
    {
        $numberRangeArray = explode(self::SEPARATOR_NUMBER_RANGE_EXPRESSION, $numberRange);
        if (count($numberRangeArray) < 1) {
            return $numberRange;
        }

        $min = 1.0 * $numberRangeArray[0];
        $max = 1.0 * $numberRangeArray[1];
        $regularExpressionNumbers = range($min, $max);

        return '(' . implode('|', $regularExpressionNumbers) . ')';
    }

}
