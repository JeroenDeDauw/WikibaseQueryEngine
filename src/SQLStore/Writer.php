<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Claim;
use Wikibase\Entity;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;

/**
 * Class responsible for writing information to the SQLStore.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
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
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function insertEntity( Entity $entity ) {
		$this->entityInserter->insertEntity( $entity );
	}

	/**
	 * @see QueryStoreUpdater::updateEntity
	 *
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function updateEntity( Entity $entity ) {
		$this->entityUpdater->updateEntity( $entity );
	}

	/**
	 * @see QueryStoreUpdater::deleteEntity
	 *
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function deleteEntity( Entity $entity ) {
		$this->entityRemover->removeEntity( $entity );
	}

}
