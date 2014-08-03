<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use DataValues\StringValue;
use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRowBuilder;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;

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
		$connection = $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();

		$connection
			->expects( $this->once() )
			->method( 'insert' )
			->with( $this->equalTo( 'string' ) );

		$snakInserter = $this->newInstance( $connection );

		$snakInserter->insertSnak( $snak, SnakRole::MAIN_SNAK, new ItemId( 'Q123' ), Claim::RANK_NORMAL );
	}

	private function newInstance( Connection $connection ) {
		return new SnakInserter(
			$this->getSnakStores( $connection ),
			new SnakRowBuilder()
		);
	}

	private function getSnakStores( Connection $connection ) {
		$stringHandler = new StringHandler();
		$stringHandler->setTablePrefix( '' );

		return array(
			new ValuelessSnakStore(
				$connection,
				'string'
			),
			new ValueSnakStore(
				$connection,
				array(
					'string' => $stringHandler
				),
				SnakRole::MAIN_SNAK
			)
		);
	}

}
