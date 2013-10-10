<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\Setup;

use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\QueryEngine\SQLStore\Setup\Updater;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Setup\Updater
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UpdaterTest extends \PHPUnit_Framework_TestCase {

	// TODO: assert correct behaviour for existing tables
	public function testCallsCorrectMethods() {
		$schema = $this->newSchema();

		$tableSchemaUpdater = $this->getMock( 'Wikibase\Database\Schema\TableSchemaUpdater' );

		$tableSchemaUpdater->expects( $this->never() )
			->method( 'updateTable' );

		$tableDefinitionReader = $this->getMock( 'Wikibase\Database\Schema\TableDefinitionReader' );

		$tableDefinitionReader->expects( $this->never() )
			->method( 'readDefinition' );

		$tableBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );

		$calledForEachTable = $this->exactly( count( $schema->getTables() ) );

		$tableBuilder->expects( clone $calledForEachTable )
			->method( 'tableExists' )
			->will( $this->returnValue( false ) );

		$tableBuilder->expects( clone $calledForEachTable )
			->method( 'createTable' )
			->with( $this->isInstanceOf( 'Wikibase\Database\Schema\Definitions\TableDefinition' ) );

		$updater = new Updater(
			$schema,
			$tableSchemaUpdater,
			$tableDefinitionReader,
			$tableBuilder
		);

		$updater->update();
	}

	protected function newSchema() {
		$defaultHandlers = new DataValueHandlers();
		$storeConfig = new StoreConfig( 'foo', 'wbsql_', $defaultHandlers->getHandlers() );
		return new Schema( $storeConfig );
	}

}
