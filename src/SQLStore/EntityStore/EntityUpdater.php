<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityUpdater implements EntityUpdatingStrategy {

	private $remover;
	private $inserter;

	public function __construct( EntityRemover $remover, EntityInserter $inserter ) {
		$this->remover = $remover;
		$this->inserter = $inserter;
	}

	public function updateEntity( EntityDocument $entity ) {
		$this->remover->removeEntity( $entity );
		$this->inserter->insertEntity( $entity );
	}

	/**
	 * @param EntityDocument $entity
	 *
	 * @return boolean
	 */
	public function canUpdate( EntityDocument $entity ) {
		// TODO
		return true;
	}

}
