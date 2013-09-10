<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\QueryEngine\SQLStore\Factory;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Factory
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
class FactoryTest extends \PHPUnit_Framework_TestCase {

	private function newInstance() {
		$storeConfig = new StoreConfig( 'foo', 'bar', array() );
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		return new Factory( $storeConfig, $queryInterface );
	}

	public function testGetSchemaReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\SQLStore\Schema',
			$this->newInstance()->getSchema()
		);
	}

	public function testNewClaimInserterReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter',
			$this->newInstance()->newClaimInserter()
		);
	}

	public function testNewEntityInserterReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\SQLStore\EntityInserter',
			$this->newInstance()->newEntityInserter()
		);
	}

	public function testNewEntityTableReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\SQLStore\EntityTable',
			$this->newInstance()->newEntityTable()
		);
	}

	public function testNewSnakInserterReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter',
			$this->newInstance()->newSnakInserter()
		);
	}

}
