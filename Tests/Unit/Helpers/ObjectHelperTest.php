<?php
namespace Iresults\Core\Tests\Helpers;

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

use Iresults\Core\Helpers\ObjectHelper;
use Iresults\Core\Iresults;
use Iresults\Core\Mutable;

require_once __DIR__ . '/../Autoloader.php';

/**
 * Test case for the Object Helper
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Iresults_Tools
 *
 * @author Daniel Corn <cod@iresults.li>
 */
class ObjectHelperTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test data
	 * @var array
	 */
	protected $testData;

	public function setUp() {
		$this->testData = array(
			'owner' => array(
				'firstName' => 'Andreas',
				'lastName'	=> 'Thurnheer-Meier',
				'email' 	=> 'info@mydomain.com',
				'company'	=> (object) array(
					'name'		=> 'myCompany',
					'type'		=> 'AG',
					'country'	=> 'USA',
				)
			),
			'address' => array(
				'street' 	=> 'Bingstreet 14',
				'city'		=> 'NYC',
				'country'	=> 'USA'
			),
			'tenant' => Mutable::mutableWithArray(
				array(
					'firstName' => 'Daniel',
					'lastName'	=> 'Corn',
					'email' 	=> 'info@tenant.com',
					'company'	=> (object) array(
						'name'		=> 'tenant.com',
						'type'		=> 'AG',
						'country'	=> 'UK',
					)
				)
			)
		);
	}

	public function tearDown() {
		unset($this->testData);
	}

	/**
	 * @test
	 */
	public function getObjectForKeyPathOfObjectTest() {
		$this->assertEquals('Andreas', 				ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', $this->testData));
		$this->assertEquals('myCompany', 			ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', $this->testData));
		$this->assertEquals('info@tenant.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant.email', $this->testData));
		$this->assertEquals('AG', 					ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', $this->testData));
	}

	/**
	 * @test
	 */
	public function getObjectForKeyPathOfObjectWithNewPathDelimiterTest() {
		$oldPathDelimiter = ObjectHelper::setPathDelimiter('/');
		$this->assertEquals('Andreas', 				ObjectHelper::getObjectForKeyPathOfObject('owner/firstName', $this->testData));
		$this->assertEquals('myCompany', 			ObjectHelper::getObjectForKeyPathOfObject('owner/company/name', $this->testData));
		$this->assertEquals('info@tenant.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant/email', $this->testData));
		$this->assertEquals('AG', 					ObjectHelper::getObjectForKeyPathOfObject('tenant/company/type', $this->testData));

		ObjectHelper::setPathDelimiter($oldPathDelimiter);
		$this->assertEquals('Andreas', 				ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', $this->testData));
		$this->assertEquals('myCompany', 			ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', $this->testData));
		$this->assertEquals('info@tenant.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant.email', $this->testData));
		$this->assertEquals('AG', 					ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', $this->testData));
	}

	/**
	 * @test
	 */
	public function setObjectForKeyPathOfObjectTest() {
		ObjectHelper::setObjectForKeyPathOfObject('owner.firstName', 		'Peter', 			$this->testData);
		ObjectHelper::setObjectForKeyPathOfObject('owner.company.name', 	'Another.Company', 	$this->testData);
		ObjectHelper::setObjectForKeyPathOfObject('tenant.email', 			'test@domain.com', 	$this->testData);
		ObjectHelper::setObjectForKeyPathOfObject('tenant.company.type', 	'LTD', 				$this->testData);


		#$this->assertEquals('Peter', 				ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', 		$this->testData));
		$this->assertEquals('Another.Company', 		ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', 	$this->testData));
		#$this->assertEquals('test@domain.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant.email', 			$this->testData));
		$this->assertEquals('LTD', 					ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', 	$this->testData));
	}

	/**
	 * @test
	 */
	public function setObjectForKeyPathOfObjectWithNewPathDelimiterTest() {
		$oldPathDelimiter = ObjectHelper::setPathDelimiter('/');
		ObjectHelper::setObjectForKeyPathOfObject('owner/firstName', 		'Peter', 			$this->testData);
		ObjectHelper::setObjectForKeyPathOfObject('owner/company/name', 	'Another.Company', 	$this->testData);
		ObjectHelper::setObjectForKeyPathOfObject('tenant/email', 			'test@domain.com', 	$this->testData);
		ObjectHelper::setObjectForKeyPathOfObject('tenant/company/type', 	'LTD', 				$this->testData);


		#$this->assertEquals('Peter', 				ObjectHelper::getObjectForKeyPathOfObject('owner/firstName', 		$this->testData));
		$this->assertEquals('Another.Company', 		ObjectHelper::getObjectForKeyPathOfObject('owner/company/name', 	$this->testData));
		#$this->assertEquals('test@domain.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant/email', 			$this->testData));
		$this->assertEquals('LTD', 					ObjectHelper::getObjectForKeyPathOfObject('tenant/company/type', 	$this->testData));

		ObjectHelper::setPathDelimiter($oldPathDelimiter);
		#$this->assertEquals('Peter', 				ObjectHelper::getObjectForKeyPathOfObject('owner.firstName', 		$this->testData));
		$this->assertEquals('Another.Company', 			ObjectHelper::getObjectForKeyPathOfObject('owner.company.name', 	$this->testData));
		#$this->assertEquals('test@domain.com', 		ObjectHelper::getObjectForKeyPathOfObject('tenant.email', 			$this->testData));
		$this->assertEquals('LTD', 					ObjectHelper::getObjectForKeyPathOfObject('tenant.company.type', 	$this->testData));
	}


}
