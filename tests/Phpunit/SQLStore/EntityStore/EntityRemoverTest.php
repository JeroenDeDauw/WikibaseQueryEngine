<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityRemoverTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider entityProvider
	 */
	public function testRemoveEntity( Entity $entity ) {
		$snakRemover = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover' )
			->disableOriginalConstructor()
			->getMock();

		$snakRemover->expects( $this->once() )
			->method( 'removeSnaksOfSubject' )
			->with( $this->equalTo( $entity->getId() ) );

		$remover = new EntityRemover( $snakRemover );

		$remover->removeEntity( $entity );
	}

	public function entityProvider() {
		$argLists = array();

		$item = Item::newEmpty();
		$item->setId( 42 );

		$argLists[] = array( $item );


		$item = Item::newEmpty();
		$item->setId( 31337 );

		$argLists[] = array( $item );


		$property = Property::newFromType( 'string' );
		$property->setId( 9001 );

		$argLists[] = array( $property );


		$property = Property::newFromType( 'string' );
		$property->setId( 1 );
		$property->addAliases( 'en', array( 'foo', 'bar', 'baz' ) );

		$property->getStatements()->addStatement( $this->newStatement( 42 ) );

		$argLists[] = array( $property );


		$item = Item::newEmpty();
		$item->setId( 2 );
		$item->getStatements()->addStatement( $this->newStatement( 42 ) );
		$item->getStatements()->addStatement( $this->newStatement( 43 ) );
		$item->getStatements()->addStatement( $this->newStatement( 44 ) );

		$argLists[] = array( $item );

		return $argLists;
	}

	private function newStatement( $propertyNumber ) {
		$claim = new Statement( new Claim( new PropertyNoValueSnak( $propertyNumber ) ) );
		$claim->setGuid( 'guid' . $propertyNumber );
		return $claim;
	}

}
