<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\Entity;

/**
 * Use case for updating entities in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityUpdater {

	private $remover;
	private $inserter;

	/**
	 * @since 0.1
	 *
	 */
	public function __construct( EntityRemover $remover, EntityInserter $inserter ) {
		$this->remover = $remover;
		$this->inserter = $inserter;
	}

	/**
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function updateEntity( Entity $entity ) {
		$this->remover->removeEntity( $entity );
		$this->inserter->insertEntity( $entity );
	}

}
