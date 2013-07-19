<?php

namespace Wikibase\QueryEngine\Integration\SQLStore;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\StringValue;
use Wikibase\Claims;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MediaWikiQueryInterface;
use Wikibase\Database\MessageReporter;
use Wikibase\Database\MWDB\ExtendedMySQLAbstraction;
use Wikibase\Database\MWDB\ExtendedSQLiteAbstraction;
use Wikibase\Database\TableDefinition;
use Wikibase\EntityId;
use Wikibase\Item;
use Wikibase\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\Store;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\Statement;

/**
 * Tests the write operations (those exposed by Wikibase\QueryEngine\SQLStore\Writer)
 * by verifying the entities are found only when they should be.
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseQueryEngineTest
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
	 * @var Store
	 */
	protected $store;

	public function setUp() {
		if ( !defined( 'MEDIAWIKI' ) || wfGetDB( DB_MASTER )->getType() !== 'mysql' ) {
			$this->markTestSkipped( 'Can only run DescriptionMatchFinderIntegrationTest on MySQL' );
		}

		parent::setUp();

		$this->store = $this->newStore();

		$this->store->newSetup( new NullMessageReporter() )->install();
	}

	public function tearDown() {
		if ( isset( $this->store ) ) {
			$this->store->newSetup( new NullMessageReporter() )->uninstall();
		}
	}

	protected function newStore() {
		$dbConnectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		// TODO: use factory in DB component
		$queryInterface = new MediaWikiQueryInterface(
			$dbConnectionProvider,
			new ExtendedMySQLAbstraction( $dbConnectionProvider )
		);

		$config = new StoreConfig(
			'test_store',
			'integrationtest_',
			array(
				'string' => new StringHandler( new DataValueTable(
					new TableDefinition(
						'string',
						array(
							new FieldDefinition( 'value', FieldDefinition::TYPE_TEXT, false ),
						)
					),
					'value',
					'value',
					'value'
				) )
			)
		);

		$propertyDvTypeLookup = $this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $this->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->returnValue( 'string' ) );

		$config->setPropertyDataValueTypeLookup( $propertyDvTypeLookup );

		return new Store( $config, $queryInterface );
	}

	public function testInsertAndRemoveItem() {
		$item = Item::newEmpty();
		$item->setId( 8888 );

		$claim = new Statement( new PropertyValueSnak( 42, new StringValue( 'Awesome' ) ) );
		$claim->setGuid( 'a claim' );
		$item->addClaim( $claim );

		$this->store->getUpdater()->insertEntity( $item );

		$propertyDescription = new SomeProperty(
			new EntityId( 'property', 42 ),
			new ValueDescription( new StringValue( 'Awesome' ) )
		);

		$this->assertEquals(
			array( new EntityId( 'item', 8888 ) ),
			$this->findMatchingEntities( $propertyDescription )
		);

		$this->store->getUpdater()->deleteEntity( $item );

		$this->assertEquals(
			array(),
			$this->findMatchingEntities( $propertyDescription )
		);
	}

	/**
	 * @param Description $description
	 * @return int[]
	 */
	protected function findMatchingEntities( Description $description ) {
		$matchFinder = $this->store->getQueryEngine();

		$queryOptions = new QueryOptions(
			100,
			0
		);

		return $matchFinder->getMatchingEntities( $description, $queryOptions );
	}

	public function testUpdateItem() {
		$item = Item::newEmpty();
		$item->setId( 4444 );

		$claim = new Statement( new PropertyValueSnak( 42, new StringValue( 'Awesome' ) ) );
		$claim->setGuid( 'foo claim' );
		$item->addClaim( $claim );

		$this->store->getUpdater()->insertEntity( $item );

		$claim = new Statement( new PropertyValueSnak( 42, new StringValue( 'Foo' ) ) );
		$claim->setGuid( 'bar claim' );

		$item->setClaims( new Claims( array(
			$claim
		) ) );

		$this->store->getUpdater()->updateEntity( $item );

		$propertyDescription = new SomeProperty(
			new EntityId( 'property', 42 ),
			new ValueDescription( new StringValue( 'Foo' ) )
		);

		$this->assertEquals(
			array( new EntityId( 'item', 4444 ) ),
			$this->findMatchingEntities( $propertyDescription )
		);

		$propertyDescription = new SomeProperty(
			new EntityId( 'property', 42 ),
			new ValueDescription( new StringValue( 'Awesome' ) )
		);

		$this->assertEquals(
			array(),
			$this->findMatchingEntities( $propertyDescription )
		);
	}

}

class NullMessageReporter implements MessageReporter {

	/**
	 * @see MessageReporter::reportMessage
	 *
	 * @since 1.21
	 *
	 * @param string $message
	 */
	public function reportMessage( $message ) {
		// no-op
	}

}