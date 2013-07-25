<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\SnakStore;

use DataValues\StringValue;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\TableDefinition;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;
use Wikibase\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseQueryEngineTest
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
			$this->getMock( 'Wikibase\Database\QueryInterface' ),
			array(
				'string' => $this->newStringHandler()
			),
			SnakRole::MAIN_SNAK
		);
	}

	protected function newStringHandler() {
		return new StringHandler( new DataValueTable(
			new TableDefinition(
				'strings_of_doom',
				array(
					new FieldDefinition( 'value', FieldDefinition::TYPE_TEXT, false ),
				)
			),
			'value',
			'value',
			'value'
		) );
	}

	public function canStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			1,
			SnakRole::MAIN_SNAK,
			0
		) );


		return $argLists;
	}

	public function cannotStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			1,
			SnakRole::QUALIFIER,
			1
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			1,
			SnakRole::MAIN_SNAK,
			1
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			1,
			SnakRole::QUALIFIER,
			1
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			1,
			SnakRole::MAIN_SNAK,
			1
		) );

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			1,
			SnakRole::QUALIFIER,
			0
		) );

		return $argLists;
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnak( ValueSnakRow $snakRow ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$stringHandler = $this->newStringHandler();

		$queryInterface->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'strings_of_doom' ),
				$this->equalTo(
					array_merge(
						array(
							'property_id' => $snakRow->getInternalPropertyId(),
							'subject_id' => $snakRow->getInternalSubjectId(),
						),
						$stringHandler->getInsertValues( $snakRow->getValue() )
					)
				)
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
		$this->setExpectedException( 'OutOfBoundsException' );

		$store = new ValueSnakStore(
			$this->getMock( 'Wikibase\Database\QueryInterface' ),
			array(),
			SnakRole::MAIN_SNAK
		);

		$store->storeSnakRow( $snakRow );
	}

	public function testRemoveSnaksOfSubject() {
		$internalSubjectId = 4242;

		$stringHandler = $this->newStringHandler();

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$queryInterface->expects( $this->atLeastOnce() )
			->method( 'delete' )
			->with(
				$this->equalTo( $stringHandler->getDataValueTable()->getTableDefinition()->getName() ),
				$this->equalTo( array( 'subject_id' => $internalSubjectId ) )
			);

		$store = new ValueSnakStore(
			$queryInterface,
			array(
				'string' => $stringHandler
			),
			SnakRole::MAIN_SNAK
		);

		$store->removeSnaksOfSubject( $internalSubjectId );
	}

}
