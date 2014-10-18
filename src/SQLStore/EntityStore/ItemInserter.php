<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Traversable;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Statement\BestStatementsFinder;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ItemInserter implements EntityInsertionStrategy {

	private $claimInserter;

	/**
	 * @var Item
	 */
	private $item;

	public function __construct( ClaimInserter $claimInserter ) {
		$this->claimInserter = $claimInserter;
	}

	public function insertEntity( EntityDocument $entity ) {
		$this->item = $entity;

		$this->insertStandardClaims();
		$this->insertVirtualClaims();
	}

	private function insertStandardClaims() {
		$bestStatementsFinder = new BestStatementsFinder( $this->item->getStatements() );
		$statements = new StatementList( $bestStatementsFinder->getBestStatementsPerProperty() );

		$this->insertClaims( $statements->getWithUniqueMainSnaks() );
	}

	private function insertClaims( Traversable $claims ) {
		foreach ( $claims as $claim ) {
			$this->claimInserter->insertClaim(
				$claim,
				$this->item->getId()
			);
		}
	}

	private function insertVirtualClaims() {
		// TODO: obtain and insert virtual claims
	}

	/**
	 * @param EntityDocument $entity
	 *
	 * @return boolean
	 */
	public function canInsert( EntityDocument $entity ) {
		return $entity instanceof Item;
	}

}
