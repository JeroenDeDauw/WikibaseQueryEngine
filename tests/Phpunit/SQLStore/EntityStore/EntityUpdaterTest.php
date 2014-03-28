<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityUpdater;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\EntityUpdater
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityUpdaterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider entityProvider
	 */
	public function testUpdateEntity( Entity $entity ) {
		$remover = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover' )
			->disableOriginalConstructor()
			->getMock();

		$remover->expects( $this->once() )
			->method( 'removeEntity' )
			->with( $this->equalTo( $entity ) );

		$inserter = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter' )
			->disableOriginalConstructor()
			->getMock();

		$inserter->expects( $this->once() )
			->method( 'insertEntity' )
			->with( $this->equalTo( $entity ) );

		$updater = new EntityUpdater( $remover, $inserter );

		$updater->updateEntity( $entity );
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


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q2' ) );
		$item->addClaim( $this->newClaim( 42 ) );
		$item->addClaim( $this->newClaim( 43 ) );
		$item->addClaim( $this->newClaim( 44 ) );

		$argLists[] = array( $item );

		return $argLists;
	}

	protected function newClaim( $propertyNumber ) {
		$claim = new Claim( new PropertyNoValueSnak( $propertyNumber ) );
		$claim->setGuid( 'guid' . $propertyNumber );
		return $claim;
	}

}
