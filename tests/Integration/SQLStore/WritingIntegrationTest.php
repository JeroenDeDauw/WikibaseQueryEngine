<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claims;
use Wikibase\DataModel\Claim\Statement;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\QueryEngine\NullMessageReporter;
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
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WritingIntegrationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SQLStoreWithDependencies
	 */
	protected $store;

	public function setUp() {
		$this->store = IntegrationStoreBuilder::newStore( $this );

		try {
			$this->store->newUninstaller( new NullMessageReporter() )->uninstall();
		}
		catch ( QueryEngineException $ex ) {}

		$this->store->newInstaller()->install();
	}

	public function testInsertAndRemoveItem() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q8888' ) );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 72010 ) ) );
		$claim->setGuid( 'a claim' );
		$item->addClaim( $claim );

		$this->store->newWriter()->insertEntity( $item );

		$propertyDescription = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 72010 ) )
		);

		$this->assertEquals(
			array( new ItemId( 'Q8888' ) ),
			$this->findMatchingEntities( $propertyDescription )
		);

		$this->store->newWriter()->deleteEntity( $item );

		$this->assertEquals(
			array(),
			$this->findMatchingEntities( $propertyDescription )
		);
	}

	/**
	 * @param Description $description
	 * @return EntityId[]
	 */
	protected function findMatchingEntities( Description $description ) {
		$matchFinder = $this->store->newQueryEngine();

		$queryOptions = new QueryOptions(
			100,
			0
		);

		return $matchFinder->getMatchingEntities( $description, $queryOptions );
	}

	public function testUpdateItem() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q4444' ) );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) );
		$claim->setGuid( 'foo claim' );
		$item->addClaim( $claim );

		$this->store->newWriter()->insertEntity( $item );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 9000 ) ) );
		$claim->setGuid( 'bar claim' );

		$item->setClaims( new Claims( array(
			$claim
		) ) );

		$this->store->newWriter()->updateEntity( $item );

		$propertyDescription = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 9000 ) )
		);

		$this->assertEquals(
			array( new ItemId( 'Q4444' ) ),
			$this->findMatchingEntities( $propertyDescription )
		);

		$propertyDescription = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 1337 ) )
		);

		$this->assertEquals(
			array(),
			$this->findMatchingEntities( $propertyDescription )
		);
	}

	public function testCanInsertClaimsWithTheSameMainSnak() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1234' ) );

		$item->addClaim( $this->newStatement( 1, 'foo', 'abcd1' ) );
		$item->addClaim( $this->newStatement( 1, 'foo', 'abcd2' ) );
		$item->addClaim( $this->newStatement( 2, 'foo', 'abcd3' ) );

		$this->store->newWriter()->insertEntity( $item );

		$this->assertTrue( true );
	}

	private function newStatement( $propertyId, $stringValue, $guid ) {
		$statement = new Statement( new PropertyValueSnak( $propertyId, new StringValue( $stringValue ) ) );
		$statement->setGuid( $guid );
		return $statement;
	}

	public function testCanAddSameSnaksToAlreadyInsertedEntity() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1234' ) );

		$item->addClaim( $this->newStatement( 1, 'foo', 'abcd1' ) );
		$item->addClaim( $this->newStatement( 2, 'foo', 'abcd2' ) );

		$this->store->newWriter()->insertEntity( $item );

		$item->addClaim( $this->newStatement( 1, 'foo', 'abcd3' ) );

		$this->store->newWriter()->updateEntity( $item );

		$this->assertTrue( true );
	}

}
