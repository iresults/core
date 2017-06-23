<?php

namespace Iresults\Core\Model;

use InvalidArgumentException;


/**
 * The interface for classes that store objects identified by some kind of path,
 * like property key paths, tree branches, file system paths and similar.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Model
 */
interface PathAccessInterface
{
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
    public function getObjectAtPath($path);

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
     * @return    void
     *
     * @throws InvalidArgumentException if the given value is not an object.
     * @throws \Iresults\Core\Model\PathAccess\Exception\DuplicateEntry if the object already exists in the tree.
     */
    public function setObjectAtPath($path, $object);

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
    public function hasObjectAtPath($path);


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESS BY OBJECT      MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the path to the given object.
     *
     * @param object $object A object in the container
     * @return    string    The path to the given object
     *
     * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the object doesn't exist in the tree.
     */
    public function getPathOfObject($object);

    /**
     * Returns if the object exists in the container.
     *
     * @param object $object The object to search in the container
     * @return    boolean    TRUE if the object is in the tree, otherwise FALSE
     */
    public function containsObject($object);


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
    public function findAllPathsMatchingPattern($pattern, $isParsed = false);

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
    public function findPathMatchingPattern($pattern, $isParsed = false);

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
    public function findAllObjectsWithPathsMatchingPattern($pattern, $isParsed = false);

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
    public function findObjectWithPathMatchingPattern($pattern, $isParsed = false);

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
    public function findObjectMostSimilarToPath($path);

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
    public function findPathMostSimilarToPath($path);

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
    public function findObjectsWithProperty($propertyKey, $propertyValue);


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MANIPULATING PATHS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the path to the parent node of the node at the given path.
     *
     * @param string $path A path
     * @return    string    The path to the parent
     */
    public function getParentPathOfPath($path);
}
