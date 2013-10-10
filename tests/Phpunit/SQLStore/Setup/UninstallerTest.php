<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\Setup;

use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\QueryEngine\SQLStore\Setup\Uninstaller;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Setup\Uninstaller
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UninstallerTest extends \PHPUnit_Framework_TestCase {

	public function testUninstall() {
		$defaultHandlers = new DataValueHandlers();
		$storeConfig = new StoreConfig( 'foo', 'wbsql_', $defaultHandlers->getHandlers() );
		$schema = new Schema( $storeConfig );
		$tableBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );

		$tableBuilder->expects( $this->atLeastOnce() )
			->method( 'dropTable' )
			->will( $this->returnValue( true ) );

		$storeSetup = new Uninstaller(
			$storeConfig,
			$schema,
			$tableBuilder
		);

		$storeSetup->uninstall();
	}

}
