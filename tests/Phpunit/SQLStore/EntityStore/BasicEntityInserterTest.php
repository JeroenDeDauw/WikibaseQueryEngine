<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\QueryEngine\SQLStore\EntityStore\BasicEntityInserter;
use Wikibase\QueryEngine\Tests\Fixtures\SpySnakInserter;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\BasicEntityInserter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ItemInserterTest extends \PHPUnit_Framework_TestCase {

	public function testOnlyBestStatementsGetInserted() {
		$item = new Item();
		$item->setId( 42 );

		$item->getStatements()->addStatement( $this->newStatement( 1, 'foo', Statement::RANK_DEPRECATED ) );
		$item->getStatements()->addStatement( $this->newStatement( 1, 'bar', Statement::RANK_PREFERRED ) );
		$item->getStatements()->addStatement( $this->newStatement( 1, 'baz', Statement::RANK_NORMAL ) );
		$item->getStatements()->addStatement( $this->newStatement( 2, 'bah', Statement::RANK_NORMAL ) );
		$item->getStatements()->addStatement( $this->newStatement( 3, 'blah', Statement::RANK_DEPRECATED ) );

		$this->assertStatementsAreInsertedForItem(
			$item,
			array(
				new PropertyValueSnak( 1, new StringValue( 'bar' ) ),
				new PropertyValueSnak( 2, new StringValue( 'bah' ) )
			)
		);
	}

	private function newStatement( $propertyId, $stringValue, $rank ) {
		$statement = new Statement( new PropertyValueSnak( $propertyId, new StringValue( $stringValue ) ) );
		$statement->setRank( $rank );
		$statement->setGuid( sha1( $propertyId .  $stringValue ) );
		return $statement;
	}

	private function assertStatementsAreInsertedForItem( Item $item, array $expectedSnaks ) {
		$snakInserter = new SpySnakInserter();

		$inserter = new BasicEntityInserter( $snakInserter );

		$inserter->insertEntity( $item );

		$this->assertEquals( $expectedSnaks, $snakInserter->getInsertedSnaks() );
	}

}

