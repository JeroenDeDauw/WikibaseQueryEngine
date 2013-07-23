<?php

namespace Wikibase\QueryEngine\SQLStore;

use OutOfBoundsException;
use Wikibase\EntityId;

/**
 * Transforms entity types and numbers into internal store ids.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Denny Vrandecic
 */
class EntityIdTransformer implements InternalEntityIdFinder, InternalEntityIdInterpreter {

	protected $stringTypeToInt;
	protected $intTypeToString;

	/**
	 * @param int[] $idMap Maps entity types (strings) to a unique one digit integer
	 */
	public function __construct( array $idMap ) {
		$this->stringTypeToInt = $idMap;
	}

	/**
	 * @see InternalEntityIdFinder::getInternalIdForEntity
	 *
	 * @param EntityId $entityId
	 *
	 * @return int
	 */
	public function getInternalIdForEntity( EntityId $entityId ) {
		$this->ensureEntityStringTypeIsKnown( $entityId->getEntityType() );

		return $this->getComputedId( $entityId );
	}

	protected function ensureEntityStringTypeIsKnown( $entityType ) {
		if ( !array_key_exists( $entityType, $this->stringTypeToInt ) ) {
			throw new OutOfBoundsException( "Id of unknown entity type '$entityType' cannot be transformed" );
		}
	}

	protected function getComputedId( EntityId $entityId ) {
		return $entityId->getNumericId() * 10 + $this->stringTypeToInt[$entityId->getEntityType()];
	}

	/**
	 * @see InternalEntityIdInterpreter::getExternalIdForEntity
	 *
	 * @param int $internalEntityId
	 *
	 * @return EntityId
	 */
	public function getExternalIdForEntity( $internalEntityId ) {
		$this->buildIntToStringMap();

		$numericId = (int)floor( $internalEntityId / 10 );
		$typeId = $internalEntityId % 10;

		$this->ensureEntityIntTypeIsKnown( $typeId );
		$typeId = $this->intTypeToString[$typeId];

		return new EntityId( $typeId, $numericId );
	}

	protected function buildIntToStringMap() {
		if ( is_array( $this->intTypeToString ) ) {
			return;
		}

		$this->intTypeToString = array();

		foreach ( $this->stringTypeToInt as $string => $int ) {
			$this->intTypeToString[$int] = $string;
		}
	}

	protected function ensureEntityIntTypeIsKnown( $intType ) {
		if ( !array_key_exists( $intType, $this->intTypeToString ) ) {
			throw new OutOfBoundsException( "Id of unknown entity type '$intType' cannot be interpreted" );
		}
	}

}