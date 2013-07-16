<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

/**
 * Represents a row in the claims table.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimRow {

	protected $internalId;
	protected $externalGuid;
	protected $internalSubjectId;
	protected $internalPropertyId;
	protected $rank;
	protected $hash;

	/**
	 * @param int|null $internalId
	 * @param string $externalGuid
	 * @param int $internalSubjectId
	 * @param int $internalPropertyId
	 * @param int $rank
	 * @param string $hash
	 */
	public function __construct( $internalId, $externalGuid, $internalSubjectId, $internalPropertyId, $rank, $hash ) {
		$this->internalId = $internalId;
		$this->externalGuid = $externalGuid;
		$this->internalSubjectId = $internalSubjectId;
		$this->internalPropertyId = $internalPropertyId;
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
	 * @return int
	 */
	public function getInternalSubjectId() {
		return $this->internalSubjectId;
	}

	/**
	 * @return int
	 */
	public function getInternalPropertyId() {
		return $this->internalPropertyId;
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
