<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\ClaimStore;

use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\SnakList;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder;

/**
 * @covers Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimRowBuilderTest extends \PHPUnit_Framework_TestCase {

	public function claimProvider() {
		/**
		 * @var Claim[] $claims
		 */
		$claims = array();

		$claims[] = new Claim(
			new PropertyValueSnak( 42, new StringValue( 'NyanData' ) )
		);

		$claims[] = new Statement(
			new Claim(
				new PropertyNoValueSnak( 1 ),
				new SnakList( array(
					new PropertyValueSnak( 2, new StringValue( 'NyanData' ) ),
					new PropertyNoValueSnak( 3 )
				) )
			)
		);

		$argLists = array();

		foreach ( $claims as $claim ) {
			$claim->setGuid( 'some-claim-guid' );
			$argLists[] = array( $claim );
		}

		return $argLists;
	}

	/**
	 * @dataProvider claimProvider
	 */
	public function testNewClaimRow( Claim $claim ) {
		$builder = new ClaimRowBuilder();

		$claimRow = $builder->newClaimRow( $claim, new ItemId( 'Q1337' ) );

		$this->assertEquals( $claim->getPropertyId(), $claimRow->getPropertyId() );
		$this->assertEquals( 'Q1337', $claimRow->getSubjectId() );
		$this->assertEquals( 'some-claim-guid', $claimRow->getExternalGuid() );
		$this->assertEquals( $claim->getHash(), $claimRow->getHash() );
		$this->assertInternalType( 'int', $claimRow->getRank() );

		$this->assertInstanceOf( 'Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRow', $claimRow );
	}

}
