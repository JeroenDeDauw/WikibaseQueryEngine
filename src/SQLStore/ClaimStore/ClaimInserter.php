<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;

/**
 * Use case for inserting snaks into the store.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimInserter {

	private $snakInserter;
	private $claimRowBuilder;

	public function __construct( SnakInserter $snakInserter, ClaimRowBuilder $claimRowBuilder ) {
		$this->snakInserter = $snakInserter;
		$this->claimRowBuilder = $claimRowBuilder;
	}

	public function insertClaim( Claim $claim, EntityId $subjectId ) {
		$this->insertSnaks( $claim, $subjectId );
	}

	private function insertSnaks( Claim $claim, EntityId $subjectId ) {
		$this->insertSnak( $claim->getMainSnak(), SnakRole::MAIN_SNAK, $subjectId, $claim->getRank() );

		foreach ( $claim->getQualifiers() as $qualifier ) {
			$this->insertSnak( $qualifier, SnakRole::QUALIFIER, $subjectId, $claim->getRank() );
		}
	}

	private function insertSnak( Snak $snak, $snakRole, EntityId $subjectId, $claimRank ) {
		$this->snakInserter->insertSnak( $snak, $snakRole, $subjectId, $claimRank );
	}

}
