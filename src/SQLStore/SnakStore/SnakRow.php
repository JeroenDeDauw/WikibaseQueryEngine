<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;

/**
 * Represents a row in a snak table. Immutable.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SnakRow {

	protected $propertyId;
	protected $snakRole;
	protected $subjectId;

	/**
	 * @param string $propertyId
	 * @param int $snakRole
	 * @param string $subjectId
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $propertyId, $snakRole, $subjectId ) {
		if ( !is_string( $propertyId ) ) {
			throw new InvalidArgumentException( '$propertyId needs to be a string' );
		}

		if ( !is_int( $snakRole ) ) {
			throw new InvalidArgumentException( '$snakRole needs to be an integer' );
		}

		if ( !is_string( $subjectId ) ) {
			throw new InvalidArgumentException( '$subjectId needs to be a string' );
		}

		$this->propertyId = $propertyId;
		$this->snakRole = $snakRole;
		$this->subjectId = $subjectId;
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
	 * @return string
	 */
	public function getSubjectId() {
		return $this->subjectId;
	}

}
