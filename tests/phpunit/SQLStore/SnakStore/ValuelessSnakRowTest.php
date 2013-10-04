<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\SnakStore;

use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;
use Wikibase\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow
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
class ValuelessSnakRowTest extends \PHPUnit_Framework_TestCase {

	public function constructorProvider() {
		$argLists = array();

		$argLists[] = array(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P9001',
			SnakRole::MAIN_SNAK,
			'Q321'
		);

		$argLists[] = array(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P9002',
			SnakRole::QUALIFIER,
			'Q123'
		);

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstructor( $internalSnakType, $propertyId, $snakRole, $subjectId ) {
		$snakRow = new ValuelessSnakRow( $internalSnakType, $propertyId, $snakRole, $subjectId );

		$this->assertEquals( $propertyId, $snakRow->getPropertyId() );
		$this->assertEquals( $snakRole, $snakRow->getSnakRole() );
		$this->assertEquals( $internalSnakType, $snakRow->getInternalSnakType() );
		$this->assertEquals( $subjectId, $snakRow->getSubjectId() );
	}

}
