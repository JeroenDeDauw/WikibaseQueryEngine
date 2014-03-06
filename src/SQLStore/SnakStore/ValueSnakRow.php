<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use DataValues\DataValue;
use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityId;

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
	 * @param EntityId $subjectId
	 * @param int $statementRank
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( DataValue $value, $propertyId, $snakRole, EntityId $subjectId, $statementRank ) {
		parent::__construct( $propertyId, $snakRole, $subjectId, $statementRank );

		$this->value = $value;
	}

	/**
	 * @return DataValue
	 */
	public function getValue() {
		return $this->value;
	}

}