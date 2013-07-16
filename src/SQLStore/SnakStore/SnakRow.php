<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

/**
 * Represents a row in a snak table. Immutable.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SnakRow {

	protected $internalPropertyId;
	protected $internalClaimId;
	protected $snakRole;
	protected $internalSubjectId;

	/**
	 * @param int $internalPropertyId
	 * @param int $internalClaimId
	 * @param int $snakRole
	 * @param int $internalSubjectId
	 */
	public function __construct( $internalPropertyId, $internalClaimId, $snakRole, $internalSubjectId ) {
		$this->internalPropertyId = $internalPropertyId;
		$this->internalClaimId = $internalClaimId;
		$this->snakRole = $snakRole;
		$this->internalSubjectId = $internalSubjectId;
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
	public function getInternalClaimId() {
		return $this->internalClaimId;
	}

	/**
	 * @return int
	 */
	public function getSnakRole() {
		return $this->snakRole;
	}

	/**
	 * @return int
	 */
	public function getInternalSubjectId() {
		return $this->internalSubjectId;
	}

}
