<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\ClaimStore;

use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRow;
use Wikibase\DataModel\Claim\Statement;

/**
 * @covers Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRow
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimRowTest extends \PHPUnit_Framework_TestCase {

	public function constructorProvider() {
		$argLists = array();

		$argLists[] = array( 1, 'foo-bar-baz-guid', 'Q2', 'P3', Statement::RANK_NORMAL, sha1( 'danweeds' ) );
		$argLists[] = array( 2, 'foo-bar-guid', 'Q2', 'P2', Statement::RANK_DEPRECATED, sha1( 'NyanData' ) );

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstruct( $internalId, $externalGuid, $subjectId, $propertyId, $rank, $hash ) {
		$claim = new ClaimRow( $internalId, $externalGuid, $subjectId, $propertyId, $rank, $hash );

		$this->assertEquals( $internalId, $claim->getInternalId() );
		$this->assertEquals( $externalGuid, $claim->getExternalGuid() );
		$this->assertEquals( $subjectId, $claim->getSubjectId() );
		$this->assertEquals( $propertyId, $claim->getPropertyId() );
		$this->assertEquals( $rank, $claim->getRank() );
		$this->assertEquals( $hash, $claim->getHash() );
	}

}
