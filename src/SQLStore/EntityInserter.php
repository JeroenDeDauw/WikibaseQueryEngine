<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\Entity;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;

/**
 * Use case for inserting entities into the store.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityInserter {

	private $claimInserter;
	private $idFinder;

	/**
	 * @since 0.1
	 *
	 * @param ClaimInserter $claimInserter
	 * @param InternalEntityIdFinder $idFinder
	 */
	public function __construct( ClaimInserter $claimInserter, InternalEntityIdFinder $idFinder ) {
		$this->claimInserter = $claimInserter;
		$this->idFinder = $idFinder;
	}

	/**
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function insertEntity( Entity $entity ) {
		$internalSubjectId = $this->getInternalId( $entity->getId() );

		foreach ( $entity->getClaims() as $claim ) {
			$this->claimInserter->insertClaim(
				$claim,
				$internalSubjectId
			);
		}

		// TODO: obtain and insert virtual claims
	}

	protected function getInternalId( EntityId $entityId ) {
		return $this->idFinder->getInternalIdForEntity( $entityId );
	}

}
