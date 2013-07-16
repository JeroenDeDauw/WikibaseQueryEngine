<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

use Wikibase\Claim;
use Wikibase\EntityId;
use Wikibase\QueryEngine\SQLStore\InternalEntityIdFinder;
use Wikibase\Statement;

/**
 * Builder for ClaimRow objects.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimRowBuilder {

	protected $idFinder;

	public function __construct( InternalEntityIdFinder $idFinder ) {
		$this->idFinder = $idFinder;
	}

	public function newClaimRow( Claim $claim, $internalSubjectId ) {
		return new ClaimRow(
			null,
			$claim->getGuid(),
			$internalSubjectId,
			$this->getInternalIdFor( $claim->getPropertyId() ),
			$claim instanceof Statement ? $claim->getRank() : 3, // TODO
			$claim->getHash()
		);
	}

	protected function getInternalIdFor( EntityId $entityId ) {
		return $this->idFinder->getInternalIdForEntity( $entityId );
	}

}