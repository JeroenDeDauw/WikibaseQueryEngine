<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;

/**
 * Use case for removing entities from the store.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityRemover implements EntityRemovalStrategy {

	private $snakRemover;

	public function __construct( SnakRemover $snakRemover ) {
		$this->snakRemover = $snakRemover;
	}

	public function removeEntity( EntityDocument $entity ) {
		$this->snakRemover->removeSnaksOfSubject( $entity->getId() );
	}

}
