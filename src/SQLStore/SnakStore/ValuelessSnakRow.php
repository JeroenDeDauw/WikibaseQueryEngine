<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValuelessSnakRow extends SnakRow {

	const TYPE_NO_VALUE = 0;
	const TYPE_SOME_VALUE = 1;

	protected $internalSnakType;

	/**
	 * @param int $internalSnakType
	 * @param string $propertyId
	 * @param int $snakRole
	 * @param string $subjectId
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $internalSnakType, $propertyId, $snakRole, $subjectId ) {
		if ( !in_array( $internalSnakType, array( self::TYPE_NO_VALUE, self::TYPE_SOME_VALUE ), true ) ) {
			throw new InvalidArgumentException( 'Invalid internal snak type provided' );
		}

		parent::__construct( $propertyId, $snakRole, $subjectId );

		$this->internalSnakType = $internalSnakType;
	}

	/**
	 * @return int
	 */
	public function getInternalSnakType() {
		return $this->internalSnakType;
	}

}
