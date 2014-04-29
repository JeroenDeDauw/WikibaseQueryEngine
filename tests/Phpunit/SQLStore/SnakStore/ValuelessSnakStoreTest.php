<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\StringValue;
use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseSnakStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValuelessSnakStoreTest extends SnakStoreTest {

	private function newConnectionStub() {
		return $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();
	}

	protected function getInstance() {
		return $this->newInstanceWithConnection( $this->newConnectionStub() );
	}

	protected function newInstanceWithConnection( Connection $connection ) {
		return new ValuelessSnakStore(
			$connection,
			'snaks_of_doom'
		);
	}

	public function canStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P1',
			SnakRole::QUALIFIER,
			new ItemId( 'Q1' ),
			Claim::RANK_NORMAL
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P1',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q1' ),
			Claim::RANK_NORMAL
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P1',
			SnakRole::QUALIFIER,
			new ItemId( 'Q1' ),
			Claim::RANK_NORMAL
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P1',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q1' ),
			Claim::RANK_NORMAL
		) );

		return $argLists;
	}

	public function cannotStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::QUALIFIER,
			new ItemId( 'Q100' ),
			Claim::RANK_NORMAL
		) );

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q100' ),
			Claim::RANK_NORMAL
		) );

		return $argLists;
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnak( ValuelessSnakRow $snakRow ) {
		$connection = $this->newConnectionStub();

		$connection->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'snaks_of_doom' )
			);

		$store = $this->newInstanceWithConnection( $connection );

		$store->storeSnakRow( $snakRow );
	}

	public function testRemoveSnaksOfSubject() {
		$subjectId = 'Q4242';
		$tableName = 'test_snaks_nyan';

		$connection = $this->newConnectionStub();

		$connection->expects( $this->once() )
			->method( 'delete' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( array( 'subject_id' => $subjectId ) )
			);

		$store = new ValuelessSnakStore(
			$connection,
			$tableName
		);

		$store->removeSnaksOfSubject( new ItemId( $subjectId ) );
	}

}
