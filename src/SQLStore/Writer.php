<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\DataModel\Entity\Entity;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityUpdater;

/**
 * Class responsible for writing information to the SQLStore.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Writer implements QueryStoreWriter {

	private $entityInserter;
	private $entityUpdater;
	private $entityRemover;

	public function __construct( EntityInserter $inserter, EntityUpdater $updater, EntityRemover $remover ) {
		$this->entityInserter = $inserter;
		$this->entityUpdater = $updater;
		$this->entityRemover = $remover;
	}

	/**
	 * @see QueryStoreUpdater::insertEntity
	 *
	 * @param Entity $entity
	 */
	public function insertEntity( Entity $entity ) {
		$this->entityInserter->insertEntity( $entity );
	}

	/**
	 * @see QueryStoreUpdater::updateEntity
	 *
	 * @param Entity $entity
	 */
	public function updateEntity( Entity $entity ) {
		$this->entityUpdater->updateEntity( $entity );
	}

	/**
	 * @see QueryStoreUpdater::deleteEntity
	 *
	 * @param Entity $entity
	 */
	public function deleteEntity( Entity $entity ) {
		$this->entityRemover->removeEntity( $entity );
	}

}
