<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\DataModel\Entity\Item;
use Wikibase\QueryEngine\SQLStore\Writer;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Writer
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WriterTest extends \PHPUnit_Framework_TestCase {

	public function testFacadeForwardsCalls() {
		$connection = $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();

		$entityInserter = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter' )
			->disableOriginalConstructor()->getMock();

		$entityRemover = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover' )
			->disableOriginalConstructor()->getMock();

		$writer = new Writer( $connection, $entityInserter, $entityRemover );

		$entityRemover->expects( $this->exactly( 3 ) )->method( 'removeEntity' );
		$entityInserter->expects( $this->exactly( 5 ) )->method( 'insertEntity' );

		$writer->deleteEntity( new Item() );

		$writer->updateEntity( new Item() );
		$writer->updateEntity( new Item() );

		$writer->insertEntity( new Item() );
		$writer->insertEntity( new Item() );
		$writer->insertEntity( new Item() );
	}

}
