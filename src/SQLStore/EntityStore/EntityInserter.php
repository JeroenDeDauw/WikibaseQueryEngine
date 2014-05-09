<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Claim\Claims;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\PropertyId;
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

	/**
	 * @var Entity
	 */
	private $entity;

	public function __construct( ClaimInserter $claimInserter, Connection $connection ) {
		$this->claimInserter = $claimInserter;
		$this->connection = $connection;
	}

	public function insertEntity( Entity $entity ) {
		$this->entity = $entity;

		$this->connection->beginTransaction();

		$this->insertStandardClaims();
		$this->insertVirtualClaims();

		$this->connection->commit();
	}

	private function insertStandardClaims() {
		$propertyIds = array();

		foreach ( $this->entity->getClaims() as $claim ) {
			$propertyIds[$claim->getPropertyId()->getSerialization()] = true;
		}

		foreach ( $propertyIds as $propertyId => $true ) {
			$claims = new Claims( $this->entity->getClaims() );
			$this->insertClaims( $claims->getClaimsForProperty( new PropertyId( $propertyId ) )->getBestClaims() );
		}
	}

	private function insertVirtualClaims() {
		// TODO: obtain and insert virtual claims
	}

	private function insertClaims( Claims $claims ) {
		foreach ( $claims as $claim ) {
			$this->claimInserter->insertClaim(
				$claim,
				$this->entity->getId()
			);
		}
	}

}
