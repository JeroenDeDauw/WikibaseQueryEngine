<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use DataValues\DataValue;

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
	 * @param int $internalPropertyId
	 * @param int $snakRole
	 * @param $internalSubjectId
	 */
	public function __construct( DataValue $value, $internalPropertyId, $snakRole, $internalSubjectId ) {
		parent::__construct( $internalPropertyId, $snakRole, $internalSubjectId );

		$this->value = $value;
	}

	/**
	 * @return int
	 */
	public function getInternalPropertyId() {
		return $this->internalPropertyId;
	}

	/**
	 * @return DataValue
	 */
	public function getValue() {
		return $this->value;
	}

}