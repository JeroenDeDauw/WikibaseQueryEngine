<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Doctrine\DBAL\Connection;
use Traversable;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Claim\Claims;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Statement\BestStatementsFinder;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;

/**
 * Use case for inserting entities into the store.
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
		// TODO: change to EntityDocument and delegate to strategies
		$this->entity = $entity;

		$this->connection->beginTransaction();

		$this->insertStandardClaims();
		$this->insertVirtualClaims();

		$this->connection->commit();
	}

	private function insertStandardClaims() {
		$bestStatementsFinder = new BestStatementsFinder( $this->entity->getClaims() );
		$statements = new StatementList( $bestStatementsFinder->getBestStatementsPerProperty() );

		$this->insertClaims( $statements->getWithUniqueMainSnaks() );
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
