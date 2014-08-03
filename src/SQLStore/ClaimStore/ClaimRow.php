<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

use InvalidArgumentException;

/**
 * Represents a row in the claims table.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimRow {

	private $internalId;
	private $externalGuid;
	private $subjectId;
	private $propertyId;
	private $rank;
	private $hash;

	/**
	 * @param int|null $internalId
	 * @param string $externalGuid
	 * @param string $subjectId
	 * @param string $propertyId
	 * @param int $rank
	 * @param string $hash
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $internalId, $externalGuid, $subjectId, $propertyId, $rank, $hash ) {
		if ( !is_string( $propertyId ) ) {
			throw new InvalidArgumentException( '$propertyId needs to be a string' );
		}

		if ( !is_string( $subjectId ) ) {
			throw new InvalidArgumentException( '$subjectId needs to be a string' );
		}

		$this->internalId = $internalId;
		$this->externalGuid = $externalGuid;
		$this->subjectId = $subjectId;
		$this->propertyId = $propertyId;
		$this->rank = $rank;
		$this->hash = $hash;
	}

	/**
	 * @return int|null
	 */
	public function getInternalId() {
		return $this->internalId;
	}

	/**
	 * @return string
	 */
	public function getExternalGuid() {
		return $this->externalGuid;
	}

	/**
	 * @return string
	 */
	public function getSubjectId() {
		return $this->subjectId;
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
	public function getRank() {
		return $this->rank;
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return $this->hash;
	}

}
