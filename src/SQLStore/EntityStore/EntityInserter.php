<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\Entity;
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

	public function __construct( ClaimInserter $claimInserter ) {
		$this->claimInserter = $claimInserter;
	}

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
