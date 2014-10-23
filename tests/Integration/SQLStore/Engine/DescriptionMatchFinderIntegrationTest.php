<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\Engine;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\Tests\Integration\IntegrationStoreBuilder;

/**
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseQueryEngineIntegration
 *
 * @group medium
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DescriptionMatchFinderIntegrationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SQLStoreWithDependencies
	 */
	private $store;

	public function setUp() {
		$this->store = IntegrationStoreBuilder::newStore( $this );

		$this->store->newInstaller()->install();

		$this->insertEntities();
	}

	public function tearDown() {
		if ( isset( $this->store ) ) {
			$this->store->newUninstaller()->uninstall();
		}
	}

	private function insertEntities() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1112' ) );

		$statement = new Statement( new Claim( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) ) );
		$statement->setGuid( 'claim0' );
		$item->getStatements()->addStatement( $statement );

		$this->store->newWriter()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1113' ) );

		$statement = new Statement( new Claim( new PropertyValueSnak( 43, new NumberValue( 1337 ) ) ) );
		$statement->setGuid( 'claim1' );
		$item->getStatements()->addStatement( $statement );

		$this->store->newWriter()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1114' ) );

		$statement = new Statement( new Claim( new PropertyValueSnak( 42, new NumberValue( 72010 ) ) ) );
		$statement->setGuid( 'claim2' );
		$item->getStatements()->addStatement( $statement );

		$this->store->newWriter()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1115' ) );

		$statement = new Statement( new Claim( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) ) );
		$statement->setGuid( 'claim3' );
		$item->getStatements()->addStatement( $statement );

		$statement = new Statement( new Claim( new PropertyValueSnak( 43, new NumberValue( 1 ) ) ) );
		$statement->setGuid( 'claim4' );
		$item->getStatements()->addStatement( $statement );

		$this->store->newWriter()->insertEntity( $item );
	}

	/**
	 * @dataProvider somePropertyProvider
	 */
	public function testFindMatchingEntitiesWithSomeProperty( SomeProperty $description, array $expectedIds ) {
		$matchFinder = $this->store->newQueryEngine();

		$queryOptions = new QueryOptions(
			100,
			0
		);

		$matchingEntityIds = $matchFinder->getMatchingEntities( $description, $queryOptions );

		$this->assertInternalType( 'array', $matchingEntityIds );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\DataModel\Entity\EntityId', $matchingEntityIds );

		$this->assertEquals( $expectedIds, $matchingEntityIds );
	}

	public function somePropertyProvider() {
		$argLists = array();

		$argLists[] = array(
			new SomeProperty(
				new EntityIdValue( new PropertyId( 'P42' ) ),
				new ValueDescription( new NumberValue( 1337 ) )
			),
			array( new ItemId( 'Q1112' ), new ItemId( 'Q1115' ) )
		);

		$argLists[] = array(
			new SomeProperty(
				new EntityIdValue( new PropertyId( 'P1' ) ),
				new ValueDescription( new NumberValue( 1337 ) )
			),
			array()
		);

		$argLists[] = array(
			new SomeProperty(
				new EntityIdValue( new PropertyId( 'P43' ) ),
				new ValueDescription( new NumberValue( 1337 ) )
			),
			array( new ItemId( 'Q1113' ) )
		);

		$argLists[] = array(
			new SomeProperty(
				new EntityIdValue( new PropertyId( 'P42' ) ),
				new ValueDescription( new NumberValue( 72010 ) )
			),
			array( new ItemId( 'Q1114' ) )
		);

		return $argLists;
	}

}
