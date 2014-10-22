<?php

namespace Wikibase\QueryEngine\SQLStore;

use Doctrine\DBAL\Connection;
use Exception;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;
use Wikibase\QueryEngine\SQLStore\EntityStore\ItemInserter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInsertionStrategy;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemovalStrategy;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityUpdater;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityUpdatingStrategy;

/**
 * Class responsible for writing information to the SQLStore.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Writer implements QueryStoreWriter {

	private $connection;
	private $entityInserter;
	private $entityRemover;

	/**
	 * @param Connection $connection
	 * @param EntityInserter $inserter
	 * @param EntityRemover $remover
	 */
	public function __construct( Connection $connection, EntityInserter $inserter, EntityRemover $remover ) {
		$this->connection = $connection;
		$this->entityInserter = $inserter;
		$this->entityRemover = $remover;
	}

	/**
	 * @see QueryStoreUpdater::insertEntity
	 *
	 * @param EntityDocument $entity
	 */
	public function insertEntity( EntityDocument $entity ) {
		$this->entityInserter->insertEntity( $entity );
	}

	/**
	 * @see QueryStoreUpdater::updateEntity
	 *
	 * @param EntityDocument $entity
	 */
	public function updateEntity( EntityDocument $entity ) {
		$this->connection->beginTransaction();

		try {
			$this->entityRemover->removeEntity( $entity );
			$this->entityInserter->insertEntity( $entity );
		}
		catch ( QueryEngineException $ex ) {
			$this->connection->rollBack();
			throw $ex;
		}

		$this->connection->commit();
	}

	/**
	 * @see QueryStoreUpdater::deleteEntity
	 *
	 * @param EntityDocument $entity
	 */
	public function deleteEntity( EntityDocument $entity ) {
		$this->entityRemover->removeEntity( $entity );
	}

}
