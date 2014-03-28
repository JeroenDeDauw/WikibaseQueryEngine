<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Claim\Statement;
use Wikibase\DataModel\Entity\EntityId;

/**
 * Builder for ClaimRow objects.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimRowBuilder {

	public function newClaimRow( Claim $claim, EntityId $subjectId ) {
		return new ClaimRow(
			null,
			$claim->getGuid(),
			$subjectId->getSerialization(),
			$claim->getPropertyId()->getSerialization(),
			$claim instanceof Statement ? $claim->getRank() : Claim::RANK_TRUTH,
			$claim->getHash()
		);
	}

}