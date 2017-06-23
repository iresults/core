<?php

namespace Iresults\Core\Tests\Unit\Helpers;
use Iresults\Core\Helpers\ObjectHelper;
use Iresults\Core\Mutable;

/**
 * Test case for the Object Helper
 *
 * @author     Daniel Corn <cod@iresults.li>
 */
class ObjectHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test data
     *
     * @var array
     */
    protected $testData;

    public function setUp()
    {
        $this->testData = [
            'owner'   => [
                'firstName' => 'Andreas',
                'lastName'  => 'Thurnheer-Meier',
                'email'     => 'info@mydomain.com',
                'company'   => (object)[
                    'name'    => 'myCompany',
                    'type'    => 'AG',
                    'country' => 'USA',
                ],
            ],
            'address' => [
                'street'  => 'Bingstreet 14',
                'city'    => 'NYC',
                'country' => 'USA',
            ],
            'tenant'  => Mutable::mutableWithArray(
                [
                    'firstName' => 'Daniel',
                    'lastName'  => 'Corn',
                    'email'     => 'info@tenant.com',
                    'company'   => (object)[
                        'name'    => 'tenant.com',
                        'type'    => 'AG',
                        'country' => 'UK',
                    ],
                ]
            ),
        ];
    }

    public function tearDown()
    {
        unset($this->testData);
    }

    /**
     * @test
     */
    public function getObjectForKeyPathOfObjectTest()
    {
        $this->assertEquals('Andreas', ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', $this->testData));
        $this->assertEquals(
            'myCompany',
            ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', $this->testData)
        );
        $this->assertEquals(
            'info@tenant.com',
            ObjectHelper::getObjectForKeyPathOfObject('tenant.email', $this->testData)
        );
        $this->assertEquals('AG', ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', $this->testData));
    }

    /**
     * @test
     */
    public function getObjectForKeyPathOfObjectWithNewPathDelimiterTest()
    {
        $oldPathDelimiter = ObjectHelper::setPathDelimiter('/');
        $this->assertEquals('Andreas', ObjectHelper::getObjectForKeyPathOfObject('owner/firstName', $this->testData));
        $this->assertEquals(
            'myCompany',
            ObjectHelper::getObjectForKeyPathOfObject('owner/company/name', $this->testData)
        );
        $this->assertEquals(
            'info@tenant.com',
            ObjectHelper::getObjectForKeyPathOfObject('tenant/email', $this->testData)
        );
        $this->assertEquals('AG', ObjectHelper::getObjectForKeyPathOfObject('tenant/company/type', $this->testData));

        ObjectHelper::setPathDelimiter($oldPathDelimiter);
        $this->assertEquals('Andreas', ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', $this->testData));
        $this->assertEquals(
            'myCompany',
            ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', $this->testData)
        );
        $this->assertEquals(
            'info@tenant.com',
            ObjectHelper::getObjectForKeyPathOfObject('tenant.email', $this->testData)
        );
        $this->assertEquals('AG', ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', $this->testData));
    }

    /**
     * @test
     */
    public function setObjectForKeyPathOfObjectTest()
    {
        ObjectHelper::setObjectForKeyPathOfObject('owner.firstName', 'Peter', $this->testData);
        ObjectHelper::setObjectForKeyPathOfObject('owner.company.name', 'Another.Company', $this->testData);
        ObjectHelper::setObjectForKeyPathOfObject('tenant.email', 'test@domain.com', $this->testData);
        ObjectHelper::setObjectForKeyPathOfObject('tenant.company.type', 'LTD', $this->testData);


        #$this->assertEquals('Peter', 				ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', 		$this->testData));
        $this->assertEquals(
            'Another.Company',
            ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', $this->testData)
        );
        #$this->assertEquals('test@domain.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant.email', 			$this->testData));
        $this->assertEquals('LTD', ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', $this->testData));
    }

    /**
     * @test
     */
    public function setObjectForKeyPathOfObjectWithNewPathDelimiterTest()
    {
        $oldPathDelimiter = ObjectHelper::setPathDelimiter('/');
        ObjectHelper::setObjectForKeyPathOfObject('owner/firstName', 'Peter', $this->testData);
        ObjectHelper::setObjectForKeyPathOfObject('owner/company/name', 'Another.Company', $this->testData);
        ObjectHelper::setObjectForKeyPathOfObject('tenant/email', 'test@domain.com', $this->testData);
        ObjectHelper::setObjectForKeyPathOfObject('tenant/company/type', 'LTD', $this->testData);


        #$this->assertEquals('Peter', 				ObjectHelper::getObjectForKeyPathOfObject('owner/firstName', 		$this->testData));
        $this->assertEquals(
            'Another.Company',
            ObjectHelper::getObjectForKeyPathOfObject('owner/company/name', $this->testData)
        );
        #$this->assertEquals('test@domain.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant/email', 			$this->testData));
        $this->assertEquals('LTD', ObjectHelper::getObjectForKeyPathOfObject('tenant/company/type', $this->testData));

        ObjectHelper::setPathDelimiter($oldPathDelimiter);
        #$this->assertEquals('Peter', 				ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', 		$this->testData));
        $this->assertEquals(
            'Another.Company',
            ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', $this->testData)
        );
        #$this->assertEquals('test@domain.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant.email', 			$this->testData));
        $this->assertEquals('LTD', ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', $this->testData));
    }


}
