<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Doctrine\DBAL\Connection;
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
	private $connection;

	public function __construct( ClaimInserter $claimInserter, Connection $connection ) {
		$this->claimInserter = $claimInserter;
		$this->connection = $connection;
	}

	public function insertEntity( Entity $entity ) {
		$this->connection->beginTransaction();

		foreach ( $entity->getClaims() as $claim ) {
			$this->claimInserter->insertClaim(
				$claim,
				$entity->getId()
			);
		}

		$this->connection->commit();

		// TODO: obtain and insert virtual claims
	}

}
