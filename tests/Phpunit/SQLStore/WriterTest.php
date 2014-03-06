<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\Item;
use Wikibase\Property;
use Wikibase\QueryEngine\SQLStore\Writer;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Writer
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WriterTest extends \PHPUnit_Framework_TestCase {

	public function testFacadeForwardsCalls() {
		$entityInserter = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter' )
			->disableOriginalConstructor()->getMock();

		$entityUpdater = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityUpdater' )
			->disableOriginalConstructor()->getMock();

		$entityRemover = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover' )
			->disableOriginalConstructor()->getMock();

		$writer = new Writer( $entityInserter, $entityUpdater, $entityRemover );

		$entityRemover->expects( $this->exactly( 1 ) )->method( 'removeEntity' );
		$entityUpdater->expects( $this->exactly( 2 ) )->method( 'updateEntity' );
		$entityInserter->expects( $this->exactly( 3 ) )->method( 'insertEntity' );

		$writer->deleteEntity( Item::newEmpty() );

		$writer->updateEntity( Item::newEmpty() );
		$writer->updateEntity( Item::newEmpty() );

		$writer->insertEntity( Item::newEmpty() );
		$writer->insertEntity( Item::newEmpty() );
		$writer->insertEntity( Item::newEmpty() );
	}

}
