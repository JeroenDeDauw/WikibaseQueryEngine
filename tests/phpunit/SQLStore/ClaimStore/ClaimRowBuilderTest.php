<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\ClaimStore;

use DataValues\StringValue;
use Wikibase\Claim;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\PropertyNoValueSnak;
use Wikibase\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder;
use Wikibase\SnakList;
use Wikibase\Statement;

/**
 * @covers Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder
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
			new PropertyNoValueSnak( 1 ),
			new SnakList( array(
				new PropertyValueSnak( 2, new StringValue( 'NyanData' ) ),
				new PropertyNoValueSnak( 3 )
			) )
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
		$idFinder = $this->getMock( 'Wikibase\QueryEngine\SQLStore\InternalEntityIdFinder' );
		$idFinder->expects( $this->any() )
			->method( 'getInternalIdForEntity' )
			->will( $this->returnValue( 42 ) );

		$builder = new ClaimRowBuilder( $idFinder );

		$claimRow = $builder->newClaimRow( $claim, 1337 );

		$this->assertEquals( 42, $claimRow->getInternalPropertyId() );
		$this->assertEquals( 1337, $claimRow->getInternalSubjectId() );
		$this->assertEquals( 'some-claim-guid', $claimRow->getExternalGuid() );
		$this->assertEquals( $claim->getHash(), $claimRow->getHash() );
		$this->assertInternalType( 'int', $claimRow->getRank() );

		$this->assertInstanceOf( 'Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRow', $claimRow );
	}

}
