<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\StringValue;
use PHPUnit_Framework_Constraint_IsTrue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseSnakStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValueSnakStoreTest extends SnakStoreTest {

	protected function getInstance() {
		return new ValueSnakStore(
			$this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' ),
			array(
				'string' => $this->newStringHandler()
			),
			SnakRole::MAIN_SNAK
		);
	}

	protected function newStringHandler() {
		return new StringHandler();
	}

	public function canStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q100' ),
			Claim::RANK_NORMAL
		) );


		return $argLists;
	}

	public function cannotStoreProvider() {
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

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::QUALIFIER,
			new ItemId( 'Q100' ),
			Claim::RANK_NORMAL
		) );

		return $argLists;
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnak( ValueSnakRow $snakRow ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$stringHandler = $this->newStringHandler();

		$queryInterface->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'string' )
			);

		$store = new ValueSnakStore(
			$queryInterface,
			array(
				'string' => $stringHandler
			),
			SnakRole::MAIN_SNAK
		);

		$store->storeSnakRow( $snakRow );
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnakWithUnknownValueType( ValueSnakRow $snakRow ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->never() )->method( $this->anything() );

		$store = new ValueSnakStore(
			$queryInterface,
			array(),
			SnakRole::MAIN_SNAK
		);

		$store->storeSnakRow( $snakRow );
	}

	public function testRemoveSnaksOfSubject() {
		$subjectId = 'Q4242';

		$stringHandler = $this->newStringHandler();

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->atLeastOnce() )
			->method( 'delete' )
			->with(
				$this->equalTo( $stringHandler->getDataValueTable()->getTableDefinition()->getName() ),
				$this->equalTo( array( 'subject_id' => $subjectId ) )
			);

		$store = new ValueSnakStore(
			$queryInterface,
			array(
				'string' => $stringHandler
			),
			SnakRole::MAIN_SNAK
		);

		$store->removeSnaksOfSubject( new ItemId( $subjectId ) );
	}

}
