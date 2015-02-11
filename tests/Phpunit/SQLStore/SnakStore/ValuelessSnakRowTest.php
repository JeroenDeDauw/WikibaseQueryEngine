<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValuelessSnakRowTest extends \PHPUnit_Framework_TestCase {

	public function constructorProvider() {
		$argLists = [];

		$argLists[] = array(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P9001',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q321' ),
			Statement::RANK_PREFERRED
		);

		$argLists[] = array(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P9002',
			SnakRole::QUALIFIER,
			new ItemId( 'Q123' ),
			Statement::RANK_NORMAL
		);

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstructor( $internalSnakType, $propertyId, $snakRole, $subjectId, $statementRank ) {
		$snakRow = new ValuelessSnakRow( $internalSnakType, $propertyId, $snakRole, $subjectId, $statementRank );

		$this->assertEquals( $propertyId, $snakRow->getPropertyId() );
		$this->assertEquals( $snakRole, $snakRow->getSnakRole() );
		$this->assertEquals( $internalSnakType, $snakRow->getInternalSnakType() );
		$this->assertEquals( $subjectId, $snakRow->getSubjectId() );
		$this->assertEquals( $statementRank, $snakRow->getStatementRank() );
	}

}
