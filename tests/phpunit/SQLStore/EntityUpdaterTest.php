<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\Claim;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\TableDefinition;
use Wikibase\Entity;
use Wikibase\Item;
use Wikibase\Property;
use Wikibase\PropertyNoValueSnak;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\EntityUpdater;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityUpdater
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseQueryEngineTest
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
		$remover = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityRemover' )
			->disableOriginalConstructor()
			->getMock();

		$remover->expects( $this->once() )
			->method( 'removeEntity' )
			->with( $this->equalTo( $entity ) );

		$inserter = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\EntityInserter' )
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
		$item->setId( 42 );

		$argLists[] = array( $item );


		$item = Item::newEmpty();
		$item->setId( 31337 );

		$argLists[] = array( $item );


		$property = Property::newEmpty();
		$property->setDataTypeId( 'string' );
		$property->setId( 9001 );

		$argLists[] = array( $property );


		$property = Property::newEmpty();
		$property->setDataTypeId( 'string' );
		$property->setId( 1 );
		$property->addAliases( 'en', array( 'foo', 'bar', 'baz' ) );
		$property->addClaim( new Claim( new PropertyNoValueSnak( 42 ) ) );

		$argLists[] = array( $property );


		$item = Item::newEmpty();
		$item->setId( 2 );
		$item->addClaim( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$item->addClaim( new Claim( new PropertyNoValueSnak( 43 ) ) );
		$item->addClaim( new Claim( new PropertyNoValueSnak( 44 ) ) );

		$argLists[] = array( $item );

		return $argLists;
	}

}
