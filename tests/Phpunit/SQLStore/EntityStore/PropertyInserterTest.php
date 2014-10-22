<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\QueryEngine\SQLStore\EntityStore\PropertyInserter;
use Wikibase\QueryEngine\Tests\Fixtures\SpyClaimInserter;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\PropertyInserter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PropertyInserterTest extends \PHPUnit_Framework_TestCase {

	public function testOnlyBestClaimsGetInserted() {
		$property = Property::newFromType( 'kittens' );
		$property->setId( 42 );

		$property->getStatements()->addStatement( $this->newStatement( 1, 'foo', Statement::RANK_DEPRECATED ) );
		$property->getStatements()->addStatement( $this->newStatement( 1, 'bar', Statement::RANK_PREFERRED ) );
		$property->getStatements()->addStatement( $this->newStatement( 1, 'baz', Statement::RANK_NORMAL ) );
		$property->getStatements()->addStatement( $this->newStatement( 2, 'bah', Statement::RANK_NORMAL ) );
		$property->getStatements()->addStatement( $this->newStatement( 3, 'blah', Statement::RANK_DEPRECATED ) );

		$this->assertStatementsAreInsertedForProperty(
			$property,
			array(
				$this->newStatement( 1, 'bar', Statement::RANK_PREFERRED ),
				$this->newStatement( 2, 'bah', Statement::RANK_NORMAL )
			)
		);
	}

	private function newStatement( $propertyId, $stringValue, $rank ) {
		$statement = new Statement( new Claim( new PropertyValueSnak( $propertyId, new StringValue( $stringValue ) ) ) );
		$statement->setRank( $rank );
		$statement->setGuid( sha1( $propertyId .  $stringValue ) );
		return $statement;
	}

	private function assertStatementsAreInsertedForProperty( Property $property, array $statements ) {
		$claimInserter = new SpyClaimInserter();

		$inserter = new PropertyInserter( $claimInserter );

		$inserter->insertEntity( $property );

		$this->assertEquals( $statements, $claimInserter->getInsertedClaims() );
	}

}
