<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityId;

/**
 * Represents a row in a snak table. Immutable.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SnakRow {

	private $propertyId;
	private $snakRole;
	private $subjectId;
	private $entityType;
	private $statementRank;

	/**
	 * @param string $propertyId
	 * @param int $snakRole
	 * @param EntityId $subjectId
	 * @param int $statementRank
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $propertyId, $snakRole, EntityId $subjectId, $statementRank ) {
		if ( !is_string( $propertyId ) ) {
			throw new InvalidArgumentException( '$propertyId needs to be a string' );
		}

		if ( !is_int( $snakRole ) ) {
			throw new InvalidArgumentException( '$snakRole needs to be an integer' );
		}

		$this->propertyId = $propertyId;
		$this->snakRole = $snakRole;
		$this->subjectId = $subjectId;
		$this->statementRank = $statementRank;
	}

	/**
	 * @return string
	 */
	public function getPropertyId() {
		return $this->propertyId;
	}

	/**
	 * @return int
	 */
	public function getSnakRole() {
		return $this->snakRole;
	}

	/**
	 * @return EntityId
	 */
	public function getSubjectId() {
		return $this->subjectId;
	}

	/**
	 * @return int
	 */
	public function getStatementRank() {
		return $this->statementRank;
	}

}
