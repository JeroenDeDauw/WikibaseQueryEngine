<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use DataValues\DataValue;
use InvalidArgumentException;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValueSnakRow extends SnakRow {

	protected $value;

	/**
	 * @param DataValue $value
	 * @param string $propertyId
	 * @param int $snakRole
	 * @param string $subjectId
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( DataValue $value, $propertyId, $snakRole, $subjectId ) {
		parent::__construct( $propertyId, $snakRole, $subjectId );

		$this->value = $value;
	}

	/**
	 * @return DataValue
	 */
	public function getValue() {
		return $this->value;
	}

}