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
			9001,
			31337,
			SnakRole::MAIN_SNAK,
			321
		);

		$argLists[] = array(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			9002,
			1337,
			SnakRole::QUALIFIER,
			123
		);

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstructor( $internalSnakType, $internalPropertyId, $internalClaimId, $snakRole, $internalSubjectId ) {
		$snakRow = new ValuelessSnakRow( $internalSnakType, $internalPropertyId, $internalClaimId, $snakRole, $internalSubjectId );

		$this->assertEquals( $internalPropertyId, $snakRow->getInternalPropertyId() );
		$this->assertEquals( $internalClaimId, $snakRow->getInternalClaimId() );
		$this->assertEquals( $snakRole, $snakRow->getSnakRole() );
		$this->assertEquals( $internalSnakType, $snakRow->getInternalSnakType() );
		$this->assertEquals( $internalSubjectId, $snakRow->getInternalSubjectId() );
	}

}
