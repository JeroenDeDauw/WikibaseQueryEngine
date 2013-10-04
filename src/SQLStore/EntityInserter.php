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
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityInserter {

	private $claimInserter;

	/**
	 * @since 0.1
	 *
	 * @param ClaimInserter $claimInserter
	 */
	public function __construct( ClaimInserter $claimInserter ) {
		$this->claimInserter = $claimInserter;
	}

	/**
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function insertEntity( Entity $entity ) {
		foreach ( $entity->getClaims() as $claim ) {
			$this->claimInserter->insertClaim(
				$claim,
				$entity->getId()
			);
		}

		// TODO: obtain and insert virtual claims
	}

}
