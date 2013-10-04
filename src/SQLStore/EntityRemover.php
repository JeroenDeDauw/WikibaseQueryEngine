<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\Entity;
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

	private $idFinder;
	private $snakRemover;

	/**
	 * @since 0.1
	 *
	 * @param SnakRemover $snakRemover
	 * @param InternalEntityIdFinder $idFinder
	 */
	public function __construct( SnakRemover $snakRemover, InternalEntityIdFinder $idFinder ) {
		$this->idFinder = $idFinder;
		$this->snakRemover = $snakRemover;
	}

	/**
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function removeEntity( Entity $entity ) {
		$internalSubjectId = $this->getInternalId( $entity->getId() );

		$this->snakRemover->removeSnaksOfSubject( $internalSubjectId );

		// TODO: obtain and remove virtual claims
	}

	protected function getInternalId( EntityId $entityId ) {
		return $this->idFinder->getInternalIdForEntity( $entityId );
	}

}
