<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\Item;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover;

/**
 * @covers Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityRemoverTest extends \PHPUnit_Framework_TestCase {

	private function getConnection() {
		return $this->getMockBuilder( 'Doctrine\DBAL\Connection' )
			->disableOriginalConstructor()->getMock();
	}

	private function getRemovalStrategyMock() {
		return $this->getMock( 'Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemovalStrategy' );
	}

	public function testWhenRemovingWithNoStrategies_exceptionIsThrown() {
		$remover = new EntityRemover( $this->getConnection(), array() );

		$this->setExpectedException( 'Wikibase\QueryEngine\QueryEngineException' );
		$remover->removeEntity( Item::newEmpty() );
	}

	public function testWhenRemovingWithNoMatchingStrategies_exceptionIsThrown() {
		$removalStrategy = $this->getRemovalStrategyMock();

		$removalStrategy->expects( $this->once() )
			->method( 'canRemove' )
			->will( $this->returnValue( false ) );

		$remover = new EntityRemover( $this->getConnection(), array( $removalStrategy ) );

		$this->setExpectedException( 'Wikibase\QueryEngine\QueryEngineException' );
		$remover->removeEntity( Item::newEmpty() );
	}

	public function testWhenRemovingMatchingStrategy_strategyIsCalled() {
		$removalStrategy = $this->getRemovalStrategyMock();

		$removalStrategy->expects( $this->once() )
			->method( 'canRemove' )
			->will( $this->returnValue( true ) );

		$removalStrategy->expects( $this->once() )
			->method( 'removeEntity' )
			->with( $this->equalTo( Item::newEmpty() ) );

		$remover = new EntityRemover( $this->getConnection(), array( $removalStrategy ) );

		$remover->removeEntity( Item::newEmpty() );
	}

	public function testWhenRemoveFails_transactionIsRolledBack() {
		$removalStrategy = $this->getRemovalStrategyMock();

		$removalStrategy->expects( $this->once() )
			->method( 'canRemove' )
			->will( $this->returnValue( true ) );

		$removalStrategy->expects( $this->once() )
			->method( 'removeEntity' )
			->will( $this->throwException( new QueryEngineException() ) );

		$connection = $this->getConnection();

		$connection->expects( $this->once() )->method( 'beginTransaction' );
		$connection->expects( $this->once() )->method( 'rollBack' );
		$connection->expects( $this->never() )->method( 'commit' );

		$remover = new EntityRemover( $connection, array( $removalStrategy ) );

		$this->setExpectedException( 'Wikibase\QueryEngine\QueryEngineException' );
		$remover->removeEntity( Item::newEmpty() );
	}



}
