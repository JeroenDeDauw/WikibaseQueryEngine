<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\Setup;

use PHPUnit_Framework_MockObject_MockObject;
use Wikibase\QueryEngine\SQLStore\Setup\Setup;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Setup\Setup
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SetupTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	protected $installer;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	protected $updater;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	protected $uninstaller;

	public function setUp() {
		parent::setUp();

		$this->installer = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\Setup\Installer' )
			->disableOriginalConstructor()->getMock();

		$this->uninstaller = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\Setup\Uninstaller' )
			->disableOriginalConstructor()->getMock();

		$this->updater = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\Setup\Updater' )
			->disableOriginalConstructor()->getMock();
	}

	protected function newSetupFromDependencies() {
		return new Setup(
			$this->installer,
			$this->updater,
			$this->uninstaller
		);
	}

	public function testInstall() {
		$this->installer->expects( $this->once() )
			->method( 'install' );

		$this->newSetupFromDependencies()->install();
	}

	public function testUpdate() {
		$this->updater->expects( $this->once() )
			->method( 'update' );

		$this->newSetupFromDependencies()->update();
	}
	public function testUninstall() {
		$this->uninstaller->expects( $this->once() )
			->method( 'uninstall' );

		$this->newSetupFromDependencies()->uninstall();
	}

}
