<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\SnakStore;

use DataValues\DataValue;
use DataValues\MonolingualTextValue;
use DataValues\StringValue;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;
use Wikibase\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseQueryEngineTest
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
			2,
			3,
			SnakRole::QUALIFIER,
			4
		);

		$argLists[] = array(
			new MonolingualTextValue( 'en', 'foobar baz' ),
			9001,
			9002,
			SnakRole::QUALIFIER,
			9003
		);

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstructor( DataValue $value, $internalPropertyId, $internalClaimId, $snakRole, $internalSubjectId ) {
		$snakRow = new ValueSnakRow( $value, $internalPropertyId, $internalClaimId, $snakRole, $internalSubjectId );

		$this->assertTrue( $value->equals( $snakRow->getValue() ) );
		$this->assertEquals( $internalPropertyId, $snakRow->getInternalPropertyId() );
		$this->assertEquals( $internalClaimId, $snakRow->getInternalClaimId() );
		$this->assertEquals( $snakRole, $snakRow->getSnakRole() );
		$this->assertEquals( $internalSubjectId, $snakRow->getInternalSubjectId() );
	}

}
