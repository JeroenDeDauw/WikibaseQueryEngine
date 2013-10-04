<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SnakRemoverTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider subjectIdProvider
	 */
	public function testRemoveSnaksOfSubject( $subjectId ) {
		$valuelessStore = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore' )
			->disableOriginalConstructor()->getMock();
		$valueStore = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore' )
			->disableOriginalConstructor()->getMock();

		$valuelessStore->expects( $this->once() )
			->method( 'removeSnaksOfSubject' )
			->with( $this->equalTo( $subjectId ) );

		$valueStore->expects( $this->once() )
			->method( 'removeSnaksOfSubject' )
			->with( $this->equalTo( $subjectId ) );

		$snakRemover = new SnakRemover( array( $valuelessStore, $valueStore ) );

		$snakRemover->removeSnaksOfSubject( $subjectId );
	}

	public function subjectIdProvider() {
		$argLists = array();

		$argLists[] = array( new ItemId( 'Q1' ) );
		$argLists[] = array( new ItemId( 'Q10' ) );
		$argLists[] = array( new ItemId( 'Q11' ) );
		$argLists[] = array( new PropertyId( 'P4242' ) );

		return $argLists;
	}

}
