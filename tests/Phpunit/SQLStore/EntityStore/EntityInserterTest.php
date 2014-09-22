<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityInserterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider entityProvider
	 */
	public function testInsertEntity( Entity $entity ) {
		$claimInserter = $this
			->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter' )
			->disableOriginalConstructor()
			->getMock();

		$invocationMocker = $claimInserter->expects( $this->exactly( count( $entity->getClaims() ) ) )
			->method( 'insertClaim' );

		if ( count( $entity->getClaims() ) > 0 ) {
			$invocationMocker->with(
				$this->isInstanceOf( 'Wikibase\Claim' ),
				$this->isInstanceOf( 'Wikibase\DataModel\Entity\EntityId' )
			);
		}

		$connection = $this->getConnection();

		$inserter = new EntityInserter( $claimInserter, $connection );

		$inserter->insertEntity( $entity );
	}

	public function entityProvider() {
		$argLists = array();

		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q42' ) );

		$argLists[] = array( $item );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q31337' ) );

		$argLists[] = array( $item );


		$property = Property::newFromType( 'string' );
		$property->setId( new PropertyId( 'P9001' ) );

		$argLists[] = array( $property );


		$property = Property::newFromType( 'string' );
		$property->setId( new PropertyId( 'P1' ) );
		$property->addAliases( 'en', array( 'foo', 'bar', 'baz' ) );

		// TODO: re-enable with DataModel 1.1
		//$property->addClaim( $this->newClaim( 42 ) );

		$argLists[] = array( $property );


		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q2' ) );

		$item->getStatements()->addStatement( $this->newNoValueStatement( 42 ) );
		$item->getStatements()->addStatement( $this->newNoValueStatement( 43 ) );
		$item->getStatements()->addStatement( $this->newNoValueStatement( 44 ) );

		$argLists[] = array( $item );

		return $argLists;
	}

	private function newNoValueStatement( $propertyNumber ) {
		$claim = new Statement( new PropertyNoValueSnak( $propertyNumber ) );
		$claim->setGuid( 'guid' . $propertyNumber );
		return $claim;
	}

	public function testOnlyBestClaimsGetInserted() {
		$item = Item::newEmpty();
		$item->setId( 42 );

		$item->getStatements()->addStatement( $this->newStatement( 1, 'foo', Claim::RANK_DEPRECATED ) );
		$item->getStatements()->addStatement( $this->newStatement( 1, 'bar', Claim::RANK_PREFERRED ) );
		$item->getStatements()->addStatement( $this->newStatement( 1, 'baz', Claim::RANK_NORMAL ) );
		$item->getStatements()->addStatement( $this->newStatement( 2, 'bah', Claim::RANK_NORMAL ) );
		$item->getStatements()->addStatement( $this->newStatement( 3, 'blah', Claim::RANK_DEPRECATED ) );

		$this->assertClaimsAreInsertedForEntity(
			$item,
			array(
				$this->newStatement( 1, 'bar', Claim::RANK_PREFERRED ),
				$this->newStatement( 2, 'bah', Claim::RANK_NORMAL )
			)
		);
	}

	private function newStatement( $propertyId, $stringValue, $rank ) {
		$statement = new Statement( new PropertyValueSnak( $propertyId, new StringValue( $stringValue ) ) );
		$statement->setRank( $rank );
		$statement->setGuid( sha1( $propertyId .  $stringValue ) );
		return $statement;
	}

	private function assertClaimsAreInsertedForEntity( Entity $entity, array $claims ) {
		$claimInserter = new SpyClaimInserter();

		$inserter = new EntityInserter( $claimInserter, $this->getConnection() );

		$inserter->insertEntity( $entity );

		$this->assertEquals( $claims, $claimInserter->getInsertedClaims() );
	}

	private function getConnection() {
		$connection = $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();

		return $connection;
	}

}

class SpyClaimInserter extends ClaimInserter {

	private $insertedClaims = array();

	public function __construct() {}

	public function insertClaim( Claim $claim, EntityId $subjectId ) {
		$this->insertedClaims[] = $claim;
	}

	public function getInsertedClaims() {
		return $this->insertedClaims;
	}

}
