<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\StringValue;
use Wikibase\Database\QueryInterface\QueryInterface;
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

	protected function getInstance() {
		return $this->newInstanceWithQueryInterface( $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' ) );
	}

	protected function newInstanceWithQueryInterface( QueryInterface $queryInterface ) {
		return new ValuelessSnakStore(
			$queryInterface,
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
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'snaks_of_doom' )
			);

		$store = $this->newInstanceWithQueryInterface( $queryInterface );

		$store->storeSnakRow( $snakRow );
	}

	public function testRemoveSnaksOfSubject() {
		$subjectId = 'Q4242';
		$tableName = 'test_snaks_nyan';

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->once() )
			->method( 'delete' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( array( 'subject_id' => $subjectId ) )
			);

		$store = new ValuelessSnakStore(
			$queryInterface,
			$tableName
		);

		$store->removeSnaksOfSubject( new ItemId( $subjectId ) );
	}

}
