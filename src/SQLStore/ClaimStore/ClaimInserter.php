<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

use Wikibase\Claim;
use Wikibase\EntityId;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;
use Wikibase\Snak;
use Wikibase\SnakRole;

/**
 * Use case for inserting snaks into the store.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimInserter {

	protected $claimsTable;
	protected $snakInserter;
	protected $claimRowBuilder;

	public function __construct( ClaimsTable $claimsTable, SnakInserter $snakInserter, ClaimRowBuilder $claimRowBuilder ) {
		$this->claimsTable = $claimsTable;
		$this->snakInserter = $snakInserter;
		$this->claimRowBuilder = $claimRowBuilder;
	}

	/**
	 * @param Claim $claim
	 * @param int $internalSubjectId
	 */
	public function insertClaim( Claim $claim, $internalSubjectId ) {
		$internalClaimId = $this->insertIntoClaimsTable( $claim, $internalSubjectId );
		$this->insertSnaks( $claim, $internalClaimId, $internalSubjectId );
	}

	protected function insertIntoClaimsTable( Claim $claim, $internalSubjectId ) {
		$claimRow = $this->claimRowBuilder->newClaimRow( $claim, $internalSubjectId );
		return $this->claimsTable->insertClaimRow( $claimRow );
	}

	protected function insertSnaks( Claim $claim, $internalClaimId, $internalSubjectId ) {
		$this->insertSnak( $claim->getMainSnak(), SnakRole::MAIN_SNAK, $internalClaimId, $internalSubjectId );

		foreach ( $claim->getQualifiers() as $qualifier ) {
			$this->insertSnak( $qualifier, SnakRole::QUALIFIER, $internalClaimId, $internalSubjectId );
		}
	}

	protected function insertSnak( Snak $snak, $snakRole, $internalClaimId, $internalSubjectId ) {
		$this->snakInserter->insertSnak( $snak, $snakRole, $internalClaimId, $internalSubjectId );
	}

}
