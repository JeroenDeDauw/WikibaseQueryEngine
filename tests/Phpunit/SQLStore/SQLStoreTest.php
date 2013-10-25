<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SQLStore
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SQLStoreTest extends \PHPUnit_Framework_TestCase {

	protected function newInstance() {
		$storeConfig = new StoreConfig( 'foo', 'bar', array() );

		$dvTypeLookup = $this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$dvTypeLookup->expects( $this->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->returnValue( 'string' ) );

		$storeConfig->setPropertyDataValueTypeLookup( $dvTypeLookup );

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );
		$tableBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );
		$definitionReader = $this->getMock( 'Wikibase\Database\Schema\TableDefinitionReader' );
		$schemaModifier = $this->getMock( 'Wikibase\Database\Schema\SchemaModifier' );

		return new SQLStore( $storeConfig, $queryInterface, $tableBuilder, $definitionReader, $schemaModifier );
	}

	public function testGetUpdaterReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\QueryStoreWriter',
			$this->newInstance()->newWriter( $this->newMockQueryInterface() )
		);
	}

	protected function newMockQueryInterface() {
		return $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );
	}

	public function testGetQueryEngineReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\QueryEngine',
			$this->newInstance()->newQueryEngine( $this->newMockQueryInterface() )
		);
	}

}
