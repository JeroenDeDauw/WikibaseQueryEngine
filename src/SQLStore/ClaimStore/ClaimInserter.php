<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

use Wikibase\Claim;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;
use Wikibase\Snak;
use Wikibase\SnakRole;

/**
 * Use case for inserting snaks into the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimInserter {

	protected $snakInserter;
	protected $claimRowBuilder;

	public function __construct( SnakInserter $snakInserter, ClaimRowBuilder $claimRowBuilder ) {
		$this->snakInserter = $snakInserter;
		$this->claimRowBuilder = $claimRowBuilder;
	}

	/**
	 * @param Claim $claim
	 * @param int $internalSubjectId
	 */
	public function insertClaim( Claim $claim, $internalSubjectId ) {
		$this->insertSnaks( $claim, $internalSubjectId );
	}

	protected function insertSnaks( Claim $claim, $internalSubjectId ) {
		$this->insertSnak( $claim->getMainSnak(), SnakRole::MAIN_SNAK, $internalSubjectId );

		foreach ( $claim->getQualifiers() as $qualifier ) {
			$this->insertSnak( $qualifier, SnakRole::QUALIFIER, $internalSubjectId );
		}
	}

	protected function insertSnak( Snak $snak, $snakRole, $internalSubjectId ) {
		$this->snakInserter->insertSnak( $snak, $snakRole, $internalSubjectId );
	}

}
