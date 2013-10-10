<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @covers Wikibase\QueryEngine\SQLStore\StoreConfig
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreConfigTest extends \PHPUnit_Framework_TestCase {

	public function constructorProvider() {
		$argLists = array();

		$defaultHandlers = new DataValueHandlers();

		$argLists[] = array( 'Wikibase SQL Store', 'wbsql_', array(
			'string' => $defaultHandlers->getHandler( 'string' )
		) );

		$argLists[] = array( 'SQL store with new config for migration', '', $defaultHandlers->getHandlers() );

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 *
	 * @param string $storeName
	 * @param string $tablePrefix
	 * @param DataValueHandler[] $dvHandlers
	 */
	public function testConstructor( $storeName, $tablePrefix, $dvHandlers ) {
		$instance = new StoreConfig( $storeName, $tablePrefix, $dvHandlers );

		$this->assertEquals( $storeName, $instance->getStoreName(), 'Store name got set correctly' );
		$this->assertEquals( $dvHandlers, $instance->getDataValueHandlers(), 'DataValueHandlers got set correctly' );
		$this->assertEquals( $tablePrefix, $instance->getTablePrefix(), 'Table prefix got set correctly' );
	}

	public function testSetPropertyDataValueTypeLookup() {
		$instance = new StoreConfig( 'foo', 'bar', array() );

		$lookup = $this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$instance->setPropertyDataValueTypeLookup( $lookup );

		$this->assertEquals( $lookup, $instance->getPropertyDataValueTypeLookup() );
	}

	public function testSetPropertyDataValueTypeLookupNotSet() {
		$instance = new StoreConfig( 'foo', 'bar', array() );

		$this->setExpectedException( 'Exception' );
		$instance->getPropertyDataValueTypeLookup();
	}

}
