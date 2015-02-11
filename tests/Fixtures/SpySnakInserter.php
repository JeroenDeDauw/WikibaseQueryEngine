<?php

namespace Wikibase\QueryEngine\Tests\Fixtures;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpySnakInserter extends SnakInserter {

	private $insertedSnaks = [];

	public function __construct() {}

	public function insertSnak( Snak $snak, $snakRole, EntityId $subjectId, $statementRank ) {
		$this->insertedSnaks[] = $snak;
	}

	public function getInsertedSnaks() {
		return $this->insertedSnaks;
	}

}