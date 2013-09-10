<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Option\QueryOptions;
use DataValues\StringValue;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder
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
class DescriptionMatchFinderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$this->newInstanceWithMocks();
		$this->assertTrue( true );
	}

	protected function newInstanceWithMocks() {
		return new DescriptionMatchFinder(
			$this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' ),
			$this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\Schema' )
				->disableOriginalConstructor()->getMock(),
			$this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' ),
			$this->getMock( 'Wikibase\QueryEngine\SQLStore\InternalEntityIdFinder' ),
			$this->getMock( 'Wikibase\QueryEngine\SQLStore\InternalEntityIdInterpreter' )
		);
	}

	public function testFindMatchingEntitiesWithSomePropertyAnyValue() {
		$description = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new AnyValue()
		);

		$queryOptions = new QueryOptions( 100, 0 );

		$queryEngine = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryEngine->expects( $this->once() )
			->method( 'select' )
			->with(
				$this->equalTo( 'tablename' ),
				$this->equalTo( array( 'subject_id' ) )
			)
			->will( $this->returnValue( array(
				(object)array( 'subject_id' => 10 )
			) ) );

		$schema = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\Schema' )
			->disableOriginalConstructor()->getMock();

		$dvHandler = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\DataValueHandler' )
			->disableOriginalConstructor()->getMock();

		$dvHandler->expects( $this->any() )
			->method( 'getWhereConditions' )
			->will( $this->returnValue( array() ) );

		$dvTable = new DataValueTable(
			new TableDefinition( 'tablename', array( new FieldDefinition( 'dsfdfdsfds', FieldDefinition::TYPE_BOOLEAN ) ) ),
			'foo',
			'bar'
		);

		$dvHandler->expects( $this->any() )
			->method( 'getDataValueTable' )
			->will( $this->returnValue( $dvTable ) );

		$schema->expects( $this->once() )
			->method( 'getDataValueHandler' )
			->will( $this->returnValue( $dvHandler ) );

		$dvTypeLookup = $this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$idTransformer = $this->getMock( 'Wikibase\QueryEngine\SQLStore\InternalEntityIdFinder' );

		$idInterpreter = $this->getMock( 'Wikibase\QueryEngine\SQLStore\InternalEntityIdInterpreter' );

		$idInterpreter->expects( $this->atLeastOnce() )
			->method( 'getExternalIdForEntity' )
			->with( $this->equalTo( 10 ) )
			->will( $this->returnValue( new ItemId( 'Q1' ) ) );

		$matchFinder = new DescriptionMatchFinder(
			$queryEngine,
			$schema,
			$dvTypeLookup,
			$idTransformer,
			$idInterpreter
		);

		$matchingIds = $matchFinder->findMatchingEntities( $description, $queryOptions );

		$this->assertInternalType( 'array', $matchingIds );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\DataModel\Entity\EntityId', $matchingIds );
		$this->assertEquals( array( new ItemId( 'Q1' ) ), $matchingIds );
	}

	public function testFindMatchingEntitiesWithInvalidPropertyId() {
		$matchFinder = new MatchFinderWithoutConstructor();

		$description = new SomeProperty( new StringValue( 'nyan!' ), new AnyValue() );
		$queryOptions = new QueryOptions( 100, 0 );

		$this->setExpectedException( 'InvalidArgumentException' );

		$matchFinder->findMatchingEntities( $description, $queryOptions );
	}

}

class MatchFinderWithoutConstructor extends \Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder {

	public function __construct(){}

}
