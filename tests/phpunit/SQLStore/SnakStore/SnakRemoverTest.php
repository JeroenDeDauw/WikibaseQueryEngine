<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover
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
class SnakRemoverTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider subjectIdProvider
	 */
	public function testRemoveSnaksOfSubject( $internalSubjectId ) {
		$valuelessStore = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore' )
			->disableOriginalConstructor()->getMock();
		$valueStore = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore' )
			->disableOriginalConstructor()->getMock();

		$valuelessStore->expects( $this->once() )
			->method( 'removeSnaksOfSubject' )
			->with( $this->equalTo( $internalSubjectId ) );

		$valueStore->expects( $this->once() )
			->method( 'removeSnaksOfSubject' )
			->with( $this->equalTo( $internalSubjectId ) );

		$snakRemover = new SnakRemover( array( $valuelessStore, $valueStore ) );

		$snakRemover->removeSnaksOfSubject( $internalSubjectId );
	}

	public function subjectIdProvider() {
		$argLists = array();

		$argLists[] = array( 1 );
		$argLists[] = array( 10 );
		$argLists[] = array( 11 );
		$argLists[] = array( 4242 );

		return $argLists;
	}

}
