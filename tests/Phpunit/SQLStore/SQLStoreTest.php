<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

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

	private function newInstance() {
		$handlers = new DataValueHandlers();
		$handlers->addMainSnakHandler( 'string', new StringHandler() );

		$storeSchema = new StoreSchema( 'prefix_', $handlers );
		$storeConfig = new StoreConfig( 'store name' );

		return new SQLStore( $storeSchema, $storeConfig );
	}

	public function testGetUpdaterReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\QueryStoreWriter',
			$this->newInstance()->newWriter( $this->newMockConnection() )
		);
	}

	private function newMockConnection() {
		return $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();
	}

	public function testGetQueryEngineReturnType() {
		$this->assertInstanceOf(
			'Wikibase\QueryEngine\QueryEngine',
			$this->newInstance()->newQueryEngine(
				$this->newMockConnection(),
				$this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' ),
				$this->getMock( 'Wikibase\DataModel\Entity\EntityIdParser' )
			)
		);
	}

}
