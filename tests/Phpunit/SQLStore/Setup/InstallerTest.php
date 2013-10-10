<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\Setup;

use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\QueryEngine\SQLStore\Setup\Installer;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Setup\Installer
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InstallerTest extends \PHPUnit_Framework_TestCase {

	public function testInstall() {
		$defaultHandlers = new DataValueHandlers();
		$storeConfig = new StoreConfig( 'foo', 'wbsql_', $defaultHandlers->getHandlers() );
		$schema = new Schema( $storeConfig );
		$tableBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );

		$tableBuilder->expects( $this->atLeastOnce() )
			->method( 'createTable' )
			->will( $this->returnValue( true ) );

		$storeSetup = new Installer(
			$storeConfig,
			$schema,
			$tableBuilder
		);

		$storeSetup->install();
	}

}
