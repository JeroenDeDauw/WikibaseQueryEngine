<?php

namespace Wikibase\QueryEngine\SQLStore;

use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover;

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
	 *
	 * @throws QueryEngineException
	 */
	public function updateEntity( EntityDocument $entity ) {
		$this->connection->beginTransaction();

		try {
			$this->entityRemover->removeEntity( $entity );
			$this->entityInserter->insertEntity( $entity );
			$this->connection->commit();
		}
		catch ( \Exception $ex ) {
			$this->connection->rollBack();
			throw $ex;
		}
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
