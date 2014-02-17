<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Option\QueryOptions;
use DataValues\StringValue;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder
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
			$this->getMock( 'Wikibase\DataModel\Entity\EntityIdParser' )
		);
	}

	public function testFindMatchingEntitiesWithSomePropertyAnyValue() {
		$subjectId = 'Q10';

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
				(object)array( 'subject_id' => $subjectId )
			) ) );

		$schema = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\Schema' )
			->disableOriginalConstructor()->getMock();

		$dvHandler = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\DataValueHandler' )
			->disableOriginalConstructor()->getMock();

		$dvHandler->expects( $this->any() )
			->method( 'getWhereConditions' )
			->will( $this->returnValue( array() ) );

		$dvTable = new DataValueTable(
			new TableDefinition( 'tablename', array(
				new FieldDefinition( 'dsfdfdsfds', new TypeDefinition( TypeDefinition::TYPE_TINYINT ) )
			) ),
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

		$idParser = $this->getMock( 'Wikibase\DataModel\Entity\EntityIdParser' );

		$idParser->expects( $this->atLeastOnce() )
			->method( 'parse' )
			->with( $this->equalTo( $subjectId ) )
			->will( $this->returnValue( new ItemId( $subjectId ) ) );

		$matchFinder = new DescriptionMatchFinder(
			$queryEngine,
			$schema,
			$dvTypeLookup,
			$idParser
		);

		$matchingIds = $matchFinder->findMatchingEntities( $description, $queryOptions );

		$this->assertInternalType( 'array', $matchingIds );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\DataModel\Entity\EntityId', $matchingIds );
		$this->assertEquals( array( new ItemId( 'Q10' ) ), $matchingIds );
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
