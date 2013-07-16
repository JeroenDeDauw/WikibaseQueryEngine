<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\ClaimStore;

use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRow;
use Wikibase\Statement;

/**
 * @covers Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRow
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
class ClaimRowTest extends \PHPUnit_Framework_TestCase {

	public function constructorProvider() {
		$argLists = array();

		$argLists[] = array( 1, 'foo-bar-baz-guid', 2, 3, Statement::RANK_NORMAL, sha1( 'danweeds' ) );
		$argLists[] = array( 2, 'foo-bar-guid', 2, 2, Statement::RANK_DEPRECATED, sha1( 'NyanData' ) );

		return $argLists;
	}

	/**
	 * @dataProvider constructorProvider
	 */
	public function testConstruct( $internalId, $externalGuid, $internalSubjectId, $internalPropertyId, $rank, $hash ) {
		$claim = new ClaimRow( $internalId, $externalGuid, $internalSubjectId, $internalPropertyId, $rank, $hash );

		$this->assertEquals( $internalId, $claim->getInternalId() );
		$this->assertEquals( $externalGuid, $claim->getExternalGuid() );
		$this->assertEquals( $internalSubjectId, $claim->getInternalSubjectId() );
		$this->assertEquals( $internalPropertyId, $claim->getInternalPropertyId() );
		$this->assertEquals( $rank, $claim->getRank() );
		$this->assertEquals( $hash, $claim->getHash() );
	}

}
