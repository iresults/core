<?php

namespace Iresults\Core\Model;


/**
 * The interface for path containers which allow the store, analyse and find
 * objects assigned to any kind of paths, including property key paths, tree
 * branches and similar.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Model
 */
interface PathContainerInterface extends \Iresults\Core\Model\PathAccessInterface
{
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* INITIALIZATION    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Initializes a path container instance with the data from the given array.
     *
     * @param array $array The associative array|dictionary to read the data from
     * @return    \Iresults\Core\Model\PathContainerInterface
     *
     * @throws \InvalidArgumentException if the given value is not an object.
     */
    public function initWithArray($array);


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Factory method: Returns an empty path container instance.
     *
     * @return    \Iresults\Core\Model\PathContainerInterface
     */
    static public function container();

    /**
     * Factory method: Returns a path container instance with the data from the
     * given mutable.
     *
     * @param \Iresults\Core\Mutable $mutable The mutable object from which the data will be read
     * @return    \Iresults\Core\Model\PathContainerInterface
     */
    static public function containerWithMutable($mutable);

    /**
     * Factory method: Returns a path container instance with the data from the
     * given array.
     *
     * @param array $array The associative array|dictionary to read the data from
     * @return    \Iresults\Core\Model\PathContainerInterface
     */
    static public function containerWithArray($array);
}
