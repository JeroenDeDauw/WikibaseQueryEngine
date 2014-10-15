<?php

namespace Wikibase\QueryEngine\SQLStore;

use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;
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
	private $entityUpdater;
	private $entityRemover;

	public function __construct( Connection $connection, EntityInsertionStrategy $inserter,
		EntityUpdatingStrategy $updater, EntityRemovalStrategy $remover ) {

		$this->connection = $connection;
		$this->entityInserter = $inserter;
		$this->entityUpdater = $updater;
		$this->entityRemover = $remover;
	}

	/**
	 * @see QueryStoreUpdater::insertEntity
	 *
	 * @param EntityDocument $entity
	 */
	public function insertEntity( EntityDocument $entity ) {
		$this->connection->beginTransaction();
		$this->entityInserter->insertEntity( $entity );
		$this->connection->commit();
	}

	/**
	 * @see QueryStoreUpdater::updateEntity
	 *
	 * @param EntityDocument $entity
	 */
	public function updateEntity( EntityDocument $entity ) {
		$this->connection->beginTransaction();
		$this->entityUpdater->updateEntity( $entity );
		$this->connection->commit();
	}

	/**
	 * @see QueryStoreUpdater::deleteEntity
	 *
	 * @param EntityDocument $entity
	 */
	public function deleteEntity( EntityDocument $entity ) {
		$this->connection->beginTransaction();
		$this->entityRemover->removeEntity( $entity );
		$this->connection->commit();
	}

}
