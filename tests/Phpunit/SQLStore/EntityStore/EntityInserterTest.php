<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityInserterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider entityProvider
	 */
	public function testInsertEntity( Entity $entity ) {
		$claimInserter = $this
			->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter' )
			->disableOriginalConstructor()
			->getMock();

		$invocationMocker = $claimInserter->expects( $this->exactly( count( $entity->getClaims() ) ) )
			->method( 'insertClaim' );

		if ( count( $entity->getClaims() ) > 0 ) {
			$invocationMocker->with(
				$this->isInstanceOf( 'Wikibase\Claim' ),
				$this->isInstanceOf( 'Wikibase\DataModel\Entity\EntityId' )
			);
		}

		$inserter = new EntityInserter( $claimInserter );

		$inserter->insertEntity( $entity );
	}

	public function entityProvider() {
		$argLists = array();

		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q42' ) );

		$argLists[] = array( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q31337' ) );

		$argLists[] = array( $item );


		$property = Property::newFromType( 'string' );
		$property->setId( new PropertyId( 'P9001' ) );

		$argLists[] = array( $property );


		$property = Property::newFromType( 'string' );
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
