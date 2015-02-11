<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\Tests\Integration\IntegrationStoreBuilder;

/**
 * Tests the write operations (those exposed by Wikibase\QueryEngi`ne\SQLStore\Writer)
 * by verifying the entities are found only when they should be.
 *
 * @group medium
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseQueryEngineIntegration
 * @group StoreSchema
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WritingIntegrationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SQLStoreWithDependencies
	 */
	private $store;

	public function setUp() {
		$this->store = IntegrationStoreBuilder::newStore( $this );

		try {
			$this->store->newUninstaller()->uninstall();
		}
		catch ( QueryEngineException $ex ) {}

		$this->store->newInstaller()->install();
	}

	public function testInsertAndRemoveItem() {
		$item = new Item();
		$item->setId( new ItemId( 'Q8888' ) );

		$statement = new Statement( new Claim( new PropertyValueSnak( 42, new NumberValue( 72010 ) ) ) );
		$statement->setGuid( 'a claim' );
		$item->getStatements()->addStatement( $statement );

		$this->store->newWriter()->insertEntity( $item );

		$propertyDescription = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 72010 ) )
		);

		$this->assertEquals(
			array( new ItemId( 'Q8888' ) ),
			$this->getMatchingEntities( $propertyDescription )
		);

		$this->store->newWriter()->deleteEntity( $item );

		$this->assertEquals(
			[],
			$this->getMatchingEntities( $propertyDescription )
		);
	}

	/**
	 * @param Description $description
	 * @return EntityId[]
	 */
	private function getMatchingEntities( Description $description ) {
		$matchFinder = $this->store->newDescriptionMatchFinder();

		$queryOptions = new QueryOptions(
			100,
			0
		);

		return $matchFinder->getMatchingEntities( $description, $queryOptions );
	}

	public function testUpdateItem() {
		$item = new Item();
		$item->setId( new ItemId( 'Q4444' ) );

		$statement = new Statement( new Claim( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) ) );
		$statement->setGuid( 'foo claim' );
		$item->getStatements()->addStatement( $statement );

		$this->store->newWriter()->insertEntity( $item );

		$statement = new Statement( new Claim( new PropertyValueSnak( 42, new NumberValue( 9000 ) ) ) );
		$statement->setGuid( 'bar claim' );

		$item->setStatements( new StatementList( array(
			$statement
		) ) );

		$this->store->newWriter()->updateEntity( $item );

		$propertyDescription = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 9000 ) )
		);

		$this->assertEquals(
			array( new ItemId( 'Q4444' ) ),
			$this->getMatchingEntities( $propertyDescription )
		);

		$propertyDescription = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 1337 ) )
		);

		$this->assertEquals(
			[],
			$this->getMatchingEntities( $propertyDescription )
		);
	}

	public function testCanInsertClaimsWithTheSameMainSnak() {
		$item = new Item();
		$item->setId( new ItemId( 'Q1234' ) );

		$item->getStatements()->addStatement( $this->newStatement( 1, 'foo', 'abcd1' ) );
		$item->getStatements()->addStatement( $this->newStatement( 1, 'foo', 'abcd2' ) );
		$item->getStatements()->addStatement( $this->newStatement( 2, 'foo', 'abcd3' ) );

		$this->store->newWriter()->insertEntity( $item );

		$this->assertTrue( true );
	}

	private function newStatement( $propertyId, $stringValue, $guid ) {
		$statement = new Statement( new Claim( new PropertyValueSnak( $propertyId, new StringValue( $stringValue ) ) ) );
		$statement->setGuid( $guid );
		return $statement;
	}

	public function testCanAddSameSnaksToAlreadyInsertedEntity() {
		$item = new Item();
		$item->setId( new ItemId( 'Q1234' ) );

		$item->getStatements()->addStatement( $this->newStatement( 1, 'foo', 'abcd1' ) );
		$item->getStatements()->addStatement( $this->newStatement( 2, 'foo', 'abcd2' ) );

		$this->store->newWriter()->insertEntity( $item );

		$item->getStatements()->addStatement( $this->newStatement( 1, 'foo', 'abcd3' ) );

		$this->store->newWriter()->updateEntity( $item );

		$this->assertTrue( true );
	}

}
