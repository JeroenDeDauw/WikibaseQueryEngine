<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\StringValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\DataModel\Statement\Statement;
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

	private function newConnectionStub() {
		return $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();
	}

	protected function getInstance() {
		return new ValueSnakStore(
			$this->newConnectionStub(),
			array(
				'string' => $this->newStringHandler()
			),
			SnakRole::MAIN_SNAK
		);
	}

	private function newStringHandler() {
		$handler = new StringHandler();
		$handler->setTablePrefix( '' );
		return $handler;
	}

	public function canStoreProvider() {
		$argLists = [];

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q100' ),
			Statement::RANK_NORMAL
		) );

		return $argLists;
	}

	public function cannotStoreProvider() {
		$argLists = [];

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P1',
			SnakRole::QUALIFIER,
			new ItemId( 'Q1' ),
			Statement::RANK_NORMAL
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P1',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q1' ),
			Statement::RANK_NORMAL
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P1',
			SnakRole::QUALIFIER,
			new ItemId( 'Q1' ),
			Statement::RANK_NORMAL
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P1',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q1' ),
			Statement::RANK_NORMAL
		) );

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::QUALIFIER,
			new ItemId( 'Q100' ),
			Statement::RANK_NORMAL
		) );

		return $argLists;
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnak( ValueSnakRow $snakRow ) {
		$connection = $this->newConnectionStub();

		$stringHandler = $this->newStringHandler();

		$connection->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'string' )
			);

		$store = new ValueSnakStore(
			$connection,
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
		$connection = $this->newConnectionStub();

		$connection->expects( $this->never() )->method( $this->anything() );

		$store = new ValueSnakStore(
			$connection,
			[],
			SnakRole::MAIN_SNAK
		);

		$store->storeSnakRow( $snakRow );
	}

	public function testRemoveSnaksOfSubject() {
		$subjectId = 'Q4242';

		$stringHandler = $this->newStringHandler();

		$connection = $this->newConnectionStub();

		$connection->expects( $this->atLeastOnce() )
			->method( 'delete' )
			->with(
				$this->equalTo( $stringHandler->getTableName() ),
				$this->equalTo( array( 'subject_id' => $subjectId ) )
			);

		$store = new ValueSnakStore(
			$connection,
			array(
				'string' => $stringHandler
			),
			SnakRole::MAIN_SNAK
		);

		$store->removeSnaksOfSubject( new ItemId( $subjectId ) );
	}

}
