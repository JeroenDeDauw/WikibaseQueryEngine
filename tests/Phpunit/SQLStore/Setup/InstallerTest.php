<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\Setup;

use Psr\Log\NullLogger;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\Setup\Installer;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Setup\Installer
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group StoreSchema
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InstallerTest extends \PHPUnit_Framework_TestCase {

	public function testInstall() {
		$handlers = new DataValueHandlers();
		$handlers->addMainSnakHandler( 'string', new StringHandler() );
		$handlers->addQualifierHandler( 'string', new StringHandler() );

		$schemaManager = $this->getMockBuilder( 'Doctrine\DBAL\Schema\AbstractSchemaManager' )
			->disableOriginalConstructor()->setMethods( array( 'createTable' ) )->getMockForAbstractClass();

		$schemaManager->expects( $this->atLeastOnce() )
			->method( 'createTable' )
			->will( $this->returnValue( true ) );

		$storeSetup = new Installer(
			new NullLogger(),
			new StoreSchema( 'prefix_', $handlers ),
			$schemaManager
		);

		$storeSetup->install();
	}

}
