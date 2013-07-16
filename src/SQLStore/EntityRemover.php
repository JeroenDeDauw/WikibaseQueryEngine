<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Entity;
use Wikibase\EntityId;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRemover;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimsTable;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;

/**
 * Use case for removing entities from the store.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityRemover {

	private $claimsTable;
	private $idFinder;
	private $snakRemover;

	/**
	 * @since 0.1
	 *
	 * @param ClaimsTable $claimsTable
	 * @param SnakRemover $snakRemover
	 * @param InternalEntityIdFinder $idFinder
	 */
	public function __construct( ClaimsTable $claimsTable, SnakRemover $snakRemover, InternalEntityIdFinder $idFinder ) {
		$this->claimsTable = $claimsTable;
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

		$this->claimsTable->removeClaimsOfSubject( $internalSubjectId );
		$this->snakRemover->removeSnaksOfSubject( $internalSubjectId );

		// TODO: obtain and remove virtual claims
	}

	protected function getInternalId( EntityId $entityId ) {
		return $this->idFinder->getInternalIdForEntity( $entityId );
	}

}
