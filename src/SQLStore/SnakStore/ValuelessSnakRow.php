<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValuelessSnakRow extends SnakRow {

	const TYPE_NO_VALUE = 0;
	const TYPE_SOME_VALUE = 1;

	private $internalSnakType;

	/**
	 * @param int $internalSnakType
	 * @param string $propertyId
	 * @param int $snakRole
	 * @param EntityId $subjectId
	 * @param int $statementRank
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $internalSnakType, $propertyId, $snakRole, EntityId $subjectId, $statementRank ) {
		if ( !in_array( $internalSnakType, array( self::TYPE_NO_VALUE, self::TYPE_SOME_VALUE ), true ) ) {
			throw new InvalidArgumentException( 'Invalid internal snak type provided' );
		}

		parent::__construct( $propertyId, $snakRole, $subjectId, $statementRank );

		$this->internalSnakType = $internalSnakType;
	}

	/**
	 * @return int
	 */
	public function getInternalSnakType() {
		return $this->internalSnakType;
	}

}
