<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\QueryEngine\SQLStore\Store;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Store
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
class StoreTest extends \PHPUnit_Framework_TestCase {

	protected function newInstance() {
		$storeConfig = new StoreConfig( 'foo', 'bar', array() );

		$dvTypeLookup = $this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$dvTypeLookup->expects( $this->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->returnValue( 'string' ) );

		$storeConfig->setPropertyDataValueTypeLookup( $dvTypeLookup );

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );
		$tableBuilder = $this->getMock( 'Wikibase\Database\Schema\TableBuilder' );

		return new Store( $storeConfig, $queryInterface, $tableBuilder );
	}

	public function testGetNameReturnType() {
		$this->assertInternalType(
			'string',
			$this->newInstance()->getName()
		);
	}

	public function testGetUpdaterReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\QueryStoreWriter',
			$this->newInstance()->getWriter()
		);
	}

	public function testGetQueryEngineReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\QueryEngine',
			$this->newInstance()->getQueryEngine()
		);
	}

}
