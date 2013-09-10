<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\SQLStore\EntityIdTransformer;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityIdTransformer
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
 * @author Denny Vrandecic
 */
class EntityIdTransformerTest extends \PHPUnit_Framework_TestCase {

	public function testConstructAndImplementsInterfaces() {
		$transformer = new EntityIdTransformer( $this->getIdMap() );

		$this->assertInstanceOf( 'Wikibase\QueryEngine\SQLStore\InternalEntityIdFinder' , $transformer );
		$this->assertInstanceOf( 'Wikibase\QueryEngine\SQLStore\InternalEntityIdInterpreter' , $transformer );
	}

	protected function getIdMap() {
		return array(
			'item' => 0,
			'property' => 1,
			'query' => 2,
		);
	}

	/**
	 * @dataProvider idProvider
	 */
	public function testGetInternalIdForEntity( EntityId $id ) {
		$idMap = $this->getIdMap();

		$transformer = new EntityIdTransformer( $idMap );

		$internalId = $transformer->getInternalIdForEntity( $id );

		$this->assertInternalType( 'int', $internalId );

		$this->assertEquals(
			$id->getNumericId(),
			floor( $internalId / 10 ),
			'Internal id divided by 10 should result in the numeric id'
		);

		$this->assertEquals(
			$idMap[$id->getEntityType()],
			$internalId % 10,
			'The last diget of the internal id should be the number for the entity type'
		);
	}

	public function idProvider() {
		$argLists = array();

		$argLists[] = array( new ItemId( 'Q1' ) );
		$argLists[] = array( new ItemId( 'Q4' ) );
		$argLists[] = array( new ItemId( 'Q9001' ) );
		$argLists[] = array( new PropertyId( 'P42' ) );

		return $argLists;
	}

	/**
	 * @dataProvider idProvider
	 */
	public function testGetInternalIdForNotSetType( EntityId $id ) {
		$transformer = new EntityIdTransformer( array() );

		$this->setExpectedException( 'OutOfBoundsException' );

		$transformer->getInternalIdForEntity( $id );
	}

	/**
	 * @dataProvider idProvider
	 */
	public function testGetExternalIdForEntity( EntityId $id ) {
		$transformer = new EntityIdTransformer( $this->getIdMap() );

		$internalId = $transformer->getInternalIdForEntity( $id );
		$actual = $transformer->getExternalIdForEntity( $internalId );

		$this->assertEquals( $id, $actual );
	}

	/**
	 * @dataProvider internalIdProvider
	 */
	public function testGetExternalIdForNotSetType( $internalId ) {
		$transformer = new EntityIdTransformer( array() );

		$this->setExpectedException( 'OutOfBoundsException' );

		$transformer->getExternalIdForEntity( $internalId );
	}

	public function internalIdProvider() {
		$argLists = array();

		$argLists[] = array( 10 );
		$argLists[] = array( 11 );
		$argLists[] = array( 19 );
		$argLists[] = array( 123450 );
		$argLists[] = array( 123451 );
		$argLists[] = array( 123459 );

		return $argLists;
	}

}
