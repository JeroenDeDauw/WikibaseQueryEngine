<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Wikibase\QueryEngine\SQLStore\EntityIdTransformer;
use Wikibase\EntityId;

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
			'foobar' => 9,
		);
	}

	/**
	 * @dataProvider idProvider
	 */
	public function testGetInternalIdForEntity( $entityType, $numericId ) {
		$idMap = $this->getIdMap();

		$transformer = new EntityIdTransformer( $idMap );

		$internalId = $transformer->getInternalIdForEntity( new EntityId( $entityType, $numericId ) );

		$this->assertInternalType( 'int', $internalId );

		$this->assertEquals(
			$numericId,
			floor( $internalId / 10 ),
			'Internal id divided by 10 should result in the numeric id'
		);

		$this->assertEquals(
			$idMap[$entityType],
			$internalId % 10,
			'The last diget of the internal id should be the number for the entity type'
		);
	}

	public function idProvider() {
		$argLists = array();

		$argLists[] = array( 'item', 1 );
		$argLists[] = array( 'item', 4 );
		$argLists[] = array( 'item', 9001 );
		$argLists[] = array( 'property', 42 );
		$argLists[] = array( 'foobar', 500 );

		return $argLists;
	}

	/**
	 * @dataProvider idProvider
	 */
	public function testGetInternalIdForNotSetType( $entityType, $numericId ) {
		$transformer = new EntityIdTransformer( array() );

		$this->setExpectedException( 'OutOfBoundsException' );

		$transformer->getInternalIdForEntity( new EntityId( $entityType, $numericId ) );
	}

	/**
	 * @dataProvider idProvider
	 */
	public function testGetExternalIdForEntity( $entityType, $numericId ) {
		$transformer = new EntityIdTransformer( $this->getIdMap() );
		$expected = new EntityId( $entityType, $numericId );

		$internalId = $transformer->getInternalIdForEntity( $expected );
		$actual = $transformer->getExternalIdForEntity( $internalId );

		$this->assertEquals( $expected, $actual );
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
