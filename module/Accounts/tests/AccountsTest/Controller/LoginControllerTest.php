<?php

namespace AccountsTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class LoginControllerTest extends AbstractHttpControllerTestCase
{
	/**
	 * SetUps before test suite.
	 */
	public function setUp()
	{
		$this->setApplicationConfig(
			include '../../../config/application.config.php'
		);
		parent::setUp();
	}

	public function testIndexAction()
	{
		$this->dispatch('/accounts');
        $this->assertResponseStatusCode(200);
	}

	public function testProcessAction()
	{
		$mockUserTable = $this->getMockBuilder('Accounts\Model\UserTable')
								->disabledOriginalConstructor()
								->getMock();

		$mockUserTable->expects($this->once())
						->method('verifyAccountByUsername')
						->will($this->returnValue( new Accounts\Model\User() ));

		$serviceManager = $this->getApplicationServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('Accounts\Mpdel\UserTable', $mockUserTable);

		
	}
}