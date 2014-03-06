<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRowBuilder;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Snak\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\SnakRowBuilder
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SnakRowBuilderTest extends \PHPUnit_Framework_TestCase {

	public function newSnakRowProvider() {
		$argLists = array();

		$argLists[] = array(
			new PropertyNoValueSnak( 1 ),
			SnakRole::QUALIFIER
		);

		$argLists[] = array(
			new PropertyNoValueSnak( 2 ),
			SnakRole::MAIN_SNAK
		);

		$argLists[] = array(
			new PropertySomeValueSnak( 3 ),
			SnakRole::QUALIFIER
		);

		$argLists[] = array(
			new PropertyValueSnak( 4, new StringValue( 'NyanData' ) ),
			SnakRole::MAIN_SNAK
		);

		return $argLists;
	}

	/**
	 * @dataProvider newSnakRowProvider
	 */
	public function testNewSnakRow( Snak $snak, $snakRole ) {
		$builder = new SnakRowBuilder();

		$snakRow = $builder->newSnakRow( $snak, $snakRole, new ItemId( 'Q1337' ), Claim::RANK_NORMAL );

		$this->assertInstanceOf( 'Wikibase\QueryEngine\SQLStore\SnakStore\SnakRow', $snakRow );

	}

}
