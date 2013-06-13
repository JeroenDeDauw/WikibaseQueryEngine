<?php

namespace Wikibase\QueryEngine\Integration\SQLStore\Engine;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use Wikibase\Claim;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MediaWikiQueryInterface;
use Wikibase\Database\MessageReporter;
use Wikibase\Database\MWDB\ExtendedMySQLAbstraction;
use Wikibase\Database\TableDefinition;
use Wikibase\EntityId;
use Wikibase\Item;
use Wikibase\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\Store;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\Statement;

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
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
class DescriptionMatchFinderIntegrationTest extends \PHPUnit_Framework_TestCase {

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

		$this->store->getSetup( new NullMessageReporter() )->install();

		$this->insertEntities();
	}

	public function tearDown() {
		if ( isset( $this->store ) ) {
			$this->store->getSetup( new NullMessageReporter() )->uninstall();
		}
	}

	protected function newStore() {
		$dbConnectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		$queryInterface = new MediaWikiQueryInterface(
			$dbConnectionProvider,
			new ExtendedMySQLAbstraction( $dbConnectionProvider )
		);

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

		$propertyDvTypeLookup = $this->getMock( 'Wikibase\QueryEngine\SQLStore\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $this->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->returnValue( 'number' ) );

		$config->setPropertyDataValueTypeLookup( $propertyDvTypeLookup );

		return new Store( $config, $queryInterface );
	}

	protected function insertEntities() {
		$item = Item::newEmpty();
		$item->setId( 1112 );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) );
		$item->addClaim( $claim );

		$this->store->getUpdater()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( 1113 );

		$claim = new Statement( new PropertyValueSnak( 43, new NumberValue( 1337 ) ) );
		$item->addClaim( $claim );

		$this->store->getUpdater()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( 1114 );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 72010 ) ) );
		$item->addClaim( $claim );

		$this->store->getUpdater()->insertEntity( $item );


		$item = Item::newEmpty();
		$item->setId( 1115 );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) );
		$item->addClaim( $claim );

		$claim = new Statement( new PropertyValueSnak( 43, new NumberValue( 1 ) ) );
		$item->addClaim( $claim );

		$this->store->getUpdater()->insertEntity( $item );
	}

	/**
	 * @dataProvider somePropertyProvider
	 */
	public function testFindMatchingEntitiesWithSomeProperty( SomeProperty $description, array $expectedIds ) {
		$matchFinder = $this->store->getDescriptionMatchFinder();

		$queryOptions = new QueryOptions(
			100,
			0
		);

		$matchingEntityIds = $matchFinder->findMatchingEntities( $description, $queryOptions );

		$this->assertInternalType( 'array', $matchingEntityIds );
		$this->assertContainsOnly( 'int', $matchingEntityIds );

		$this->assertEquals( $expectedIds, $matchingEntityIds );
	}

	public function somePropertyProvider() {
		$argLists = array();

		$argLists[] = array(
			new SomeProperty(
				new EntityId( 'property', 42 ),
				new ValueDescription( new NumberValue( 1337 ) )
			),
			array( 11120, 11150 )
		);

		$argLists[] = array(
			new SomeProperty(
				new EntityId( 'property', 1 ),
				new ValueDescription( new NumberValue( 1337 ) )
			),
			array()
		);

		$argLists[] = array(
			new SomeProperty(
				new EntityId( 'property', 43 ),
				new ValueDescription( new NumberValue( 1337 ) )
			),
			array( 11130 )
		);

		$argLists[] = array(
			new SomeProperty(
				new EntityId( 'property', 42 ),
				new ValueDescription( new NumberValue( 72010 ) )
			),
			array( 11140 )
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