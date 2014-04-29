<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\Entity;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;

/**
 * Use case for removing entities from the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityRemover {

	private $snakRemover;

	public function __construct( SnakRemover $snakRemover ) {
		$this->snakRemover = $snakRemover;
	}

	public function removeEntity( Entity $entity ) {
		$this->snakRemover->removeSnaksOfSubject( $entity->getId() );

		// TODO: obtain and remove virtual claims
	}

}
