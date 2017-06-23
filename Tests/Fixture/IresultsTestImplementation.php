<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 05.10.16 12:16
 */


namespace Iresults\Core\Tests\Fixture;
use Iresults\Core\Base;
use Iresults\Core\IresultsBaseInterface;

class IresultsTestImplementation extends Base implements IresultsBaseInterface
{
    public function isFullRequest()
    {
        return false;
    }

    public function dynamicFunction()
    {
        return true;
    }
}
