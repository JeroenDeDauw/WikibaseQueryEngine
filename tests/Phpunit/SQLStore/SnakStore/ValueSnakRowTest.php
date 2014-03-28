<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\DataValue;
use DataValues\MonolingualTextValue;
use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValueSnakRowTest extends \PHPUnit_Framework_TestCase {

	public function constructorProvider() {
		$argLists = array();

		$argLists[] = array(
			new StringValue( 'foobar baz' ),
			'P2',
			SnakRole::QUALIFIER,
			new ItemId( 'Q4' ),
			Claim::RANK_PREFERRED
		);

		$argLists[] = array(
			new MonolingualTextValue( 'en', 'foobar baz' ),
			'P9001',
			SnakRole::QUALIFIER,
			new ItemId( 'Q9003' ),
			Claim::RANK_NORMAL
		);

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstructor( DataValue $value, $propertyId, $snakRole, $subjectId, $statementRank ) {
		$snakRow = new ValueSnakRow( $value, $propertyId, $snakRole, $subjectId, $statementRank );

		$this->assertTrue( $value->equals( $snakRow->getValue() ) );
		$this->assertEquals( $propertyId, $snakRow->getPropertyId() );
		$this->assertEquals( $snakRole, $snakRow->getSnakRole() );
		$this->assertEquals( $subjectId, $snakRow->getSubjectId() );
		$this->assertEquals( $statementRank, $snakRow->getStatementRank() );
	}

}
