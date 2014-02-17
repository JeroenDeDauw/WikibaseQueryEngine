<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use DataValues\StringValue;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\PropertyNoValueSnak;
use Wikibase\PropertySomeValueSnak;
use Wikibase\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRowBuilder;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;
use Wikibase\Snak;
use Wikibase\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SnakInserterTest extends \PHPUnit_Framework_TestCase {

	public function snakProvider() {
		$argLists = array();

		$argLists[] = array( new PropertyNoValueSnak( 1 ) );

		$argLists[] = array( new PropertyNoValueSnak( 31337 ) );

		$argLists[] = array( new PropertySomeValueSnak( 3 ) );

		$argLists[] = array( new PropertyValueSnak( 4, new StringValue( 'NyanData' ) ) );

		return $argLists;
	}

	/**
	 * @dataProvider snakProvider
	 */
	public function testInsertSnak( Snak $snak ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface
			->expects( $this->once() )
			->method( 'insert' )
			->with( $this->equalTo( 'test_table' ) );

		$snakInserter = $this->newInstance( $queryInterface );

		$snakInserter->insertSnak( $snak, SnakRole::MAIN_SNAK, new ItemId( 'Q123' ) );
	}

	protected function newInstance( QueryInterface $queryInterface ) {
		return new SnakInserter(
			$this->getSnakStores( $queryInterface ),
			new SnakRowBuilder()
		);
	}

	protected function getSnakStores( QueryInterface $queryInterface ) {
		return array(
			new ValuelessSnakStore(
				$queryInterface,
				'test_table'
			),
			new ValueSnakStore(
				$queryInterface,
				array(
					'string' => $this->newStringHandler()
				),
				SnakRole::MAIN_SNAK
			)
		);
	}

	protected function newStringHandler() {
		return new StringHandler( new DataValueTable(
			new TableDefinition(
				'test_table',
				array(
					new FieldDefinition(
						'value',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						false
					),
				)
			),
			'value',
			'value',
			'value'
		) );
	}

}
