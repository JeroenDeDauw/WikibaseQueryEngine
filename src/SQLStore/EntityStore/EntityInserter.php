<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Doctrine\DBAL\Connection;
use Traversable;
use Wikibase\DataModel\Claim\Claim;
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
		$claims = new ClaimList( $this->entity->getClaims() );

		$this->insertClaims( $claims->getBestClaims()->getWithUniqueMainSnaks() );
	}

	private function insertVirtualClaims() {
		// TODO: obtain and insert virtual claims
	}

	private function insertClaims( Traversable $claims ) {
		foreach ( $claims as $claim ) {
			$this->claimInserter->insertClaim(
				$claim,
				$this->entity->getId()
			);
		}
	}

}

/**
 * TODO: move to DM
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimList implements \IteratorAggregate {

	/**
	 * @var Claim[]
	 */
	private $claims;

	/**
	 * @param Claim[] $claims
	 */
	public function __construct( array $claims = array() ) {
		$this->claims = array_values( $claims );
	}

	/**
	 * Returns the best claims.
	 * The best claims are those with the highest rank for a particular property.
	 * Deprecated ranks are never included.
	 *
	 * Caution: the ranking is done per property, not globally, as in the Claims class.
	 *
	 * @return self
	 */
	public function getBestClaims() {
		$claimList = new self();

		foreach ( $this->getPropertyIds() as $propertyId ) {
			$claims = new Claims( $this->claims );
			$claimList->addClaims( $claims->getClaimsForProperty( $propertyId )->getBestClaims() );
		}

		return $claimList;
	}

	/**
	 * @return PropertyId[]
	 */
	public function getPropertyIds() {
		$propertyIds = array();

		foreach ( $this->claims as $claim ) {
			$propertyIds[$claim->getPropertyId()->getSerialization()] = $claim->getPropertyId();
		}

		return array_values( $propertyIds );
	}

	private function addClaims( Claims $claims ) {
		foreach ( $claims as $claim ) {
			$this->addClaim( $claim );
		}
	}

	private function addClaim( Claim $claim ) {
		$this->claims[] = $claim;
	}

	/**
	 * Claims that have a main snak already in the list are filtered out.
	 * The last occurrences are retained.
	 *
	 * @return self
	 */
	public function getWithUniqueMainSnaks() {
		$claims = array();

		foreach ( $this->claims as $claim ) {
			$claims[$claim->getMainSnak()->getHash()] = $claim;
		}

		return new self( $claims );
	}

	/**
	 * @return Traversable
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->claims );
	}

}