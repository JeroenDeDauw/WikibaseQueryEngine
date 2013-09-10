<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;


use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Schema
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
class SchemaTest extends \PHPUnit_Framework_TestCase {

	public function testGetTables() {
		$schema = new Schema( new StoreConfig( 'foo', 'bar', array() ) );

		$tables = $schema->getTables();

		$this->assertInternalType( 'array', $tables );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\Database\Schema\Definitions\TableDefinition', $tables );

		$tableCount = count( $tables );

		$defaultHandlers = new DataValueHandlers();
		$schema = new Schema( new StoreConfig( 'foo', 'bar', $defaultHandlers->getHandlers() ) );

		$tables = $schema->getTables();

		$this->assertInternalType( 'array', $tables );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\Database\Schema\Definitions\TableDefinition', $tables );

		$this->assertEquals(
			$tableCount + count( $defaultHandlers->getHandlers() ) * 2,
			count( $tables ),
			'A schema with n more DataValue handlers should have 2n more tables (2 since there are 2 snak roles)'
		);
	}

	public function testGetDataValueHandler() {
		$defaultHandlers = new DataValueHandlers();
		$handlers = $defaultHandlers->getHandlers();
		$schema = new Schema( new StoreConfig( 'foo', 'bar', $handlers ) );

		foreach ( $handlers as $dataValueType => $handler ) {
			foreach ( array( SnakRole::MAIN_SNAK, SnakRole::QUALIFIER ) as $snakRole ) {
				$obtainedHandler = $schema->getDataValueHandler( $dataValueType, $snakRole );

				$this->assertInstanceOf( 'Wikibase\QueryEngine\SQLStore\DataValueHandler', $obtainedHandler );

				$this->assertEquals(
					get_class( $handler ),
					get_class( $obtainedHandler ),
					'The class of the handler should not change between schema initialization and later fetching'
				);
			}
		}
	}

}
