<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\Database\MWDB\ExtendedMySQLAbstraction;
use Wikibase\Database\MediaWikiQueryInterface;
use Wikibase\QueryEngine\SQLStore\Store;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\Tests\QueryStoreTest;

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
		$connectionProvider = $this->getMock( 'Wikibase\Repo\DBConnectionProvider' );

		$storeConfig = new StoreConfig( 'foo', 'bar', array() );

		$dvTypeLookup = $this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$dvTypeLookup->expects( $this->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->returnValue( 'string' ) );

		$storeConfig->setPropertyDataValueTypeLookup( $dvTypeLookup );

		$queryInterface = new MediaWikiQueryInterface(
			$connectionProvider,
			new ExtendedMySQLAbstraction( $connectionProvider )
		);

		return new Store( $storeConfig, $queryInterface );
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
			$this->newInstance()->getUpdater()
		);
	}

	public function testGetQueryEngineReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\QueryEngine',
			$this->newInstance()->getQueryEngine()
		);
	}

}
