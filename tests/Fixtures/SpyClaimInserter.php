<?php

namespace Wikibase\QueryEngine\Tests\Fixtures;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpyClaimInserter extends ClaimInserter {

	private $insertedClaims = array();

	public function __construct() {}

	public function insertClaim( Claim $claim, EntityId $subjectId ) {
		$this->insertedClaims[] = $claim;
	}

	public function getInsertedClaims() {
		return $this->insertedClaims;
	}

}
