<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\Entity;
use Wikibase\Item;
use Wikibase\Property;
use Wikibase\PropertyNoValueSnak;
use Wikibase\QueryEngine\SQLStore\EntityTable;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityTable
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityTableTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider entityProvider
	 */
	public function testInsertEntity( Entity $entity ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'nyan_entities' ),
				$this->equalTo(
					array(
						'id' => $entity->getId()->getSerialization(),
						'type' => $entity->getType(),
					)
				)
			);

		$table = new EntityTable( $queryInterface, 'nyan_entities' );

		$table->insertEntity( $entity );
	}

	public function entityProvider() {
		$argLists = array();

		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q42' ) );

		$argLists[] = array( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q31337' ) );

		$argLists[] = array( $item );


		$property = Property::newEmpty();
		$property->setDataTypeId( 'string' );
		$property->setId( new PropertyId( 'P9001' ) );

		$argLists[] = array( $property );


		$property = Property::newEmpty();
		$property->setDataTypeId( 'string' );
		$property->setId( new PropertyId( 'P1' ) );
		$property->addAliases( 'en', array( 'foo', 'bar', 'baz' ) );
		$property->addClaim( $this->newClaim( 42 ) );

		$argLists[] = array( $property );

		return $argLists;
	}

	protected function newClaim( $propertyNumber ) {
		$claim = new Claim( new PropertyNoValueSnak( $propertyNumber ) );
		$claim->setGuid( 'guid' . $propertyNumber );
		return $claim;
	}

	/**
	 * @dataProvider entityProvider
	 */
	public function testRemoveEntity( Entity $entity ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->once() )
			->method( 'delete' )
			->with(
				$this->equalTo( 'nyan_entities' ),
				$this->equalTo(
					array(
						'id' => $entity->getId()->getSerialization()
					)
				)
			);

		$table = new EntityTable( $queryInterface, 'nyan_entities' );

		$table->removeEntity( $entity );
	}

}
