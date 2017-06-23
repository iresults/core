<?php
/**
 * copyright iresults gmbh
 */

namespace Iresults\Core\Tests\Fixture;


use Iresults\Core\Enum;

class MixedEnum extends Enum
{
    const ARRAY = [1, 2];
    const IS_FALSE = false;
    const IS_TRUE = true;
    const IS_NULL = null;
}
