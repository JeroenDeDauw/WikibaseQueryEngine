<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\SnakStore;

use DataValues\StringValue;
use Wikibase\Database\QueryInterface;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore;
use Wikibase\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore
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
class ValuelessSnakStoreTest extends SnakStoreTest {

	protected function getInstance() {
		return $this->newInstanceWithQueryInterface( $this->getMock( 'Wikibase\Database\QueryInterface' ) );
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

		return $argLists;
	}

	public function cannotStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			1,
			SnakRole::QUALIFIER,
			0
		) );

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			1,
			SnakRole::MAIN_SNAK,
			0
		) );

		return $argLists;
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnak( ValuelessSnakRow $snakRow ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$queryInterface->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'snaks_of_doom' ),
				$this->equalTo(
					array(
						'property_id' => $snakRow->getInternalPropertyId(),
						'subject_id' => $snakRow->getInternalSubjectId(),
						'snak_type' => $snakRow->getInternalSnakType(),
						'snak_role' => $snakRow->getSnakRole(),
					)
				)
			);

		$store = $this->newInstanceWithQueryInterface( $queryInterface );

		$store->storeSnakRow( $snakRow );
	}

	public function testRemoveSnaksOfSubject() {
		$internalSubjectId = 4242;
		$tableName = 'test_snaks_nyan';

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );

		$queryInterface->expects( $this->once() )
			->method( 'delete' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( array( 'subject_id' => $internalSubjectId ) )
			);

		$store = new ValuelessSnakStore(
			$queryInterface,
			$tableName
		);

		$store->removeSnaksOfSubject( $internalSubjectId );
	}

}
