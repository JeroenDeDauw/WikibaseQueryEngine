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
			'Q4'
		);

		$argLists[] = array(
			new MonolingualTextValue( 'en', 'foobar baz' ),
			'P9001',
			SnakRole::QUALIFIER,
			'Q9003'
		);

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstructor( DataValue $value, $propertyId, $snakRole, $subjectId ) {
		$snakRow = new ValueSnakRow( $value, $propertyId, $snakRole, $subjectId );

		$this->assertTrue( $value->equals( $snakRow->getValue() ) );
		$this->assertEquals( $propertyId, $snakRow->getPropertyId() );
		$this->assertEquals( $snakRole, $snakRow->getSnakRole() );
		$this->assertEquals( $subjectId, $snakRow->getSubjectId() );
	}

}
