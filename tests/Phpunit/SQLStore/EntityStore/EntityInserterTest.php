<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\Item;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityInserterTest extends \PHPUnit_Framework_TestCase {

	private function getConnection() {
		return $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();
	}

	private function getInsertionStrategyMock() {
		return $this->getMock( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityInsertionStrategy' );
	}

	public function testWhenInsertingWithNoStrategies_exceptionIsThrown() {
		$inserter = new EntityInserter( $this->getConnection(), [] );

		$this->setExpectedException( 'Wikibase\QueryEngine\QueryEngineException' );
		$inserter->insertEntity( new Item() );
	}

	public function testWhenInsertingWithNoMatchingStrategies_exceptionIsThrown() {
		$insertionStrategy = $this->getInsertionStrategyMock();

		$insertionStrategy->expects( $this->once() )
			->method( 'canInsert' )
			->will( $this->returnValue( false ) );

		$inserter = new EntityInserter( $this->getConnection(), array( $insertionStrategy ) );

		$this->setExpectedException( 'Wikibase\QueryEngine\QueryEngineException' );
		$inserter->insertEntity( new Item() );
	}

	public function testWhenInsertingMatchingStrategy_strategyIsCalled() {
		$insertionStrategy = $this->getInsertionStrategyMock();

		$insertionStrategy->expects( $this->once() )
			->method( 'canInsert' )
			->will( $this->returnValue( true ) );

		$insertionStrategy->expects( $this->once() )
			->method( 'insertEntity' )
			->with( $this->equalTo( new Item() ) );

		$inserter = new EntityInserter( $this->getConnection(), array( $insertionStrategy ) );

		$inserter->insertEntity( new Item() );
	}

	public function testWhenInsertFails_transactionIsRolledBack() {
		$insertionStrategy = $this->getInsertionStrategyMock();

		$insertionStrategy->expects( $this->once() )
			->method( 'canInsert' )
			->will( $this->returnValue( true ) );

		$insertionStrategy->expects( $this->once() )
			->method( 'insertEntity' )
			->will( $this->throwException( new QueryEngineException() ) );

		$connection = $this->getConnection();

		$connection->expects( $this->once() )->method( 'beginTransaction' );
		$connection->expects( $this->once() )->method( 'rollBack' );
		$connection->expects( $this->never() )->method( 'commit' );

		$inserter = new EntityInserter( $connection, array( $insertionStrategy ) );

		$this->setExpectedException( 'Wikibase\QueryEngine\QueryEngineException' );
		$inserter->insertEntity( new Item() );
	}

}
