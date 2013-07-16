<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\ClaimStore;

use Wikibase\Database\QueryInterface;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRow;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimsTable;
use Wikibase\Statement;

/**
 * @covers  Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimsTable
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
class ClaimsTableTest extends \PHPUnit_Framework_TestCase {

	protected function getInstance( QueryInterface $queryInterface ) {
		return new ClaimsTable( $queryInterface, 'test_claims' );
	}

	public function claimRowProvider() {
		$argLists = array();

		$argLists[] = array( new ClaimRow(
			null,
			'foo-bar-guid',
			2,
			3,
			Statement::RANK_NORMAL,
			sha1( 'NyanData' )
		) );

		$argLists[] = array( new ClaimRow(
			null,
			'foo-bar-baz-guid',
			31337,
			7201010,
			Statement::RANK_PREFERRED,
			sha1( 'danweeds' )
		) );

		return $argLists;
	}

	/**
	 * @dataProvider claimRowProvider
	 */
	public function testInsertClaimRow( ClaimRow $claimRow ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );
		$queryInterface->expects( $this->once() )
			->method( 'getInsertId' )
			->will( $this->returnValue( 42 ) );

		$table = $this->getInstance( $queryInterface );

		$queryInterface->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'test_claims' )
			);

		$insertionId = $table->insertClaimRow( $claimRow );
		$this->assertInternalType( 'int', $insertionId );
		$this->assertEquals( 42, $insertionId );
	}

	public function testInsertRowWithId() {
		$claimRow = new ClaimRow(
			42,
			'foo-bar-baz-guid',
			31337,
			7201010,
			Statement::RANK_PREFERRED,
			sha1( 'danweeds' )
		);

		$table = $this->getInstance( $this->getMock( 'Wikibase\Database\QueryInterface' ) );

		$this->setExpectedException( 'InvalidArgumentException' );
		$table->insertClaimRow( $claimRow );
	}

	public function testRemoveClaimsOfSubject() {
		$tableName = 'test_claims';
		$subjectId = 1234;

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface' );
		$queryInterface->expects( $this->once() )
			->method( 'delete' )
			->with(
				$this->equalTo( $tableName ),
				$this->equalTo( array( 'subject_id' => $subjectId ) )
			);

		$table = new ClaimsTable( $queryInterface, $tableName );

		$table->removeClaimsOfSubject( $subjectId );
	}

}
