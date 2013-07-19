<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\Database\TableBuilder;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\QueryEngine\SQLStore\Setup;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Setup
 *
 * @file
 * @since 0.1
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

	public function testInstall() {
		$defaultHandlers = new DataValueHandlers();
		$storeConfig = new StoreConfig( 'foo', 'wbsql_', $defaultHandlers->getHandlers() );
		$schema = new Schema( $storeConfig );
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$queryInterface->expects( $this->atLeastOnce() )
			->method( 'createTable' )
			->will( $this->returnValue( true ) );

		$storeSetup = new Setup(
			$storeConfig,
			$schema,
			$queryInterface,
			new TableBuilder( $queryInterface )
		);

		$storeSetup->install();
	}

	public function testUninstall() {
		$defaultHandlers = new DataValueHandlers();
		$storeConfig = new StoreConfig( 'foo', 'wbsql_', $defaultHandlers->getHandlers() );
		$schema = new Schema( $storeConfig );
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$queryInterface->expects( $this->atLeastOnce() )
			->method( 'dropTable' )
			->will( $this->returnValue( true ) );

		$storeSetup = new Setup(
			$storeConfig,
			$schema,
			$queryInterface,
			new TableBuilder( $queryInterface )
		);

		$storeSetup->uninstall();
	}

	public function testSetMessageReporter() {
		$defaultHandlers = new DataValueHandlers();
		$storeConfig = new StoreConfig( 'foo', 'wbsql_', $defaultHandlers->getHandlers() );
		$schema = new Schema( $storeConfig );
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$queryInterface->expects( $this->atLeastOnce() )
			->method( 'dropTable' )
			->will( $this->returnValue( true ) );

		$storeSetup = new Setup(
			$storeConfig,
			$schema,
			$queryInterface,
			new TableBuilder( $queryInterface )
		);

		$messageReporter = $this->getMock( 'Wikibase\Database\MessageReporter' );

		$messageReporter->expects( $this->atLeastOnce() )
			->method( 'reportMessage' );

		$storeSetup->setMessageReporter( $messageReporter );

		$storeSetup->uninstall();
	}

}
