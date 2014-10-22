<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryEngineException;

/**
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityInserter {

	private $connection;
	private $entityInserters;

	/**
	 * @param Connection $connection
	 * @param EntityInsertionStrategy[] $entityInserters
	 */
	public function __construct( Connection $connection, array $entityInserters ) {
		$this->connection = $connection;
		$this->entityInserters = $entityInserters;
	}

	public function insertEntity( EntityDocument $entity ) {
		$inserter = $this->getEntityInserterFor( $entity );

		$this->connection->beginTransaction();

		try {
			$inserter->insertEntity( $entity );
		}
		catch ( QueryEngineException $ex ) {
			$this->connection->rollBack();
			throw $ex;
		}

		$this->connection->commit();
	}

	/**
	 * @param EntityDocument $entity
	 * @return EntityInsertionStrategy
	 * @throws QueryEngineException
	 */
	private function getEntityInserterFor( EntityDocument $entity ) {
		foreach ( $this->entityInserters as $entityInserter ) {
			if ( $entityInserter->canInsert( $entity ) ) {
				return $entityInserter;
			}
		}

		throw new QueryEngineException( 'There is no insertion strategy for ' . $entity->getId() );
	}

}
