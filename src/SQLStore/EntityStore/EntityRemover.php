<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryEngineException;

/**
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityRemover {

	private $connection;
	private $entityRemovers;

	/**
	 * @param Connection $connection
	 * @param EntityRemovalStrategy[] $entityRemovers
	 */
	public function __construct( Connection $connection, array $entityRemovers ) {
		$this->connection = $connection;
		$this->entityRemovers = $entityRemovers;
	}

	public function removeEntity( EntityDocument $entity ) {
		$inserter = $this->getEntityRemoverFor( $entity );

		$this->connection->beginTransaction();

		try {
			$inserter->removeEntity( $entity );
		}
		catch ( QueryEngineException $ex ) {
			$this->connection->rollBack();
			throw $ex;
		}

		$this->connection->commit();
	}

	/**
	 * @param EntityDocument $entity
	 * @return EntityRemovalStrategy
	 * @throws QueryEngineException
	 */
	private function getEntityRemoverFor( EntityDocument $entity ) {
		foreach ( $this->entityRemovers as $entityInserter ) {
			if ( $entityInserter->canRemove( $entity ) ) {
				return $entityInserter;
			}
		}

		throw new QueryEngineException( 'There is no removal strategy for ' . $entity->getId() );
	}

}
