<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use Wikibase\DataModel\Claim\Claims;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\QueryEngine\NullMessageReporter;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\DataModel\Claim\Statement;

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
		if ( !defined( 'MEDIAWIKI' ) || !in_array( wfGetDB( DB_MASTER )->getType(), array( 'mysql', 'sqlite' ) ) ) {
			$this->markTestSkipped( 'Can only run DescriptionMatchFinderIntegrationTest on MySQL or SQLite' );
		}

		parent::setUp();

		$this->store = $this->newStore();

		$this->store->newInstaller()->install();
	}

	public function tearDown() {
		if ( isset( $this->store ) ) {
			$this->store->newUninstaller( new NullMessageReporter() )->uninstall();
		}
	}

	protected function newStore() {
		return IntegrationStoreBuilder::newStore( $this );
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

}
