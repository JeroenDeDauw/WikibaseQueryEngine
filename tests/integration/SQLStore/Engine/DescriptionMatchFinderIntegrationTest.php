<?php

namespace Wikibase\QueryEngine\Integration\SQLStore\Engine;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MediaWiki\MediaWikiQueryInterface;
use Wikibase\Database\MediaWiki\MWTableBuilderBuilder;
use Wikibase\Database\MessageReporter;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\Item;
use Wikibase\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\Store;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\Statement;

/**
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseQueryEngineIntegration
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DescriptionMatchFinderIntegrationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Store
	 */
	protected $store;

	public function setUp() {
		if ( !defined( 'MEDIAWIKI' ) || !in_array( wfGetDB( DB_MASTER )->getType(), array( 'mysql', 'sqlite' ) ) ) {
			$this->markTestSkipped( 'Can only run DescriptionMatchFinderIntegrationTest on MySQL and SQLite' );
		}

		parent::setUp();

		$this->store = $this->newStore();

		$this->store->newSetup( new NullMessageReporter() )->install();

		$this->insertEntities();
	}

	public function tearDown() {
		if ( isset( $this->store ) ) {
			$this->store->newSetup( new NullMessageReporter() )->uninstall();
		}
	}

	protected function newStore() {
		$dbConnectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		$tbBuilder = new MWTableBuilderBuilder();
		$tableBuilder = $tbBuilder->setConnection( $dbConnectionProvider )->getTableBuilder();

		$queryInterface = new MediaWikiQueryInterface( $dbConnectionProvider );

		$config = new StoreConfig(
			'test_store',
			'integrationtest_',
			array(
				'number' => new NumberHandler( new DataValueTable(
					new TableDefinition(
						'number_table',
						array(
							new FieldDefinition( 'value', FieldDefinition::TYPE_FLOAT, false ),
							new FieldDefinition( 'json', FieldDefinition::TYPE_TEXT, false ),
						)
					),
					'json',
					'value',
					'value'
				) )
			)
		);

		$propertyDvTypeLookup = $this->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $this->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->returnValue( 'number' ) );

		$config->setPropertyDataValueTypeLookup( $propertyDvTypeLookup );

		return new Store( $config, $queryInterface, $tableBuilder );
	}

	protected function insertEntities() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1112' ) );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) );
		$claim->setGuid( 'claim0' );
		$item->addClaim( $claim );

		$this->store->getWriter()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1113' ) );

		$claim = new Statement( new PropertyValueSnak( 43, new NumberValue( 1337 ) ) );
		$claim->setGuid( 'claim1' );
		$item->addClaim( $claim );

		$this->store->getWriter()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1114' ) );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 72010 ) ) );
		$claim->setGuid( 'claim2' );
		$item->addClaim( $claim );

		$this->store->getWriter()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q1115' ) );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) );
		$claim->setGuid( 'claim3' );
		$item->addClaim( $claim );

		$claim = new Statement( new PropertyValueSnak( 43, new NumberValue( 1 ) ) );
		$claim->setGuid( 'claim4' );
		$item->addClaim( $claim );

		$this->store->getWriter()->insertEntity( $item );
	}

	/**
	 * @dataProvider somePropertyProvider
	 */
	public function testFindMatchingEntitiesWithSomeProperty( SomeProperty $description, array $expectedIds ) {
		$matchFinder = $this->store->getQueryEngine();

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