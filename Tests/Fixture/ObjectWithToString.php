<?php
/**
 * @author COD
 * Created 11.10.16 10:59
 */


namespace Iresults\Core\Tests\Fixture;


class ObjectWithToString
{
    public function __toString()
    {
        return __METHOD__;

    }
}