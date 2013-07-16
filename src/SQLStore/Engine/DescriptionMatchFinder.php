<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use InvalidArgumentException;
use Wikibase\Database\QueryInterface;
use Wikibase\EntityId;
use Wikibase\Lib\EntityIdParser;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\InternalEntityIdFinder;
use Wikibase\QueryEngine\SQLStore\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\SnakRole;

/**
 * Simple query engine that works on top of the SQLStore.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DescriptionMatchFinder {

	protected $queryInterface;
	protected $schema;
	protected $propertyDataValueTypeLookup;
	protected $idFinder;

	public function __construct( QueryInterface $queryInterface,
			Schema $schema,
			PropertyDataValueTypeLookup $propertyDataValueTypeLookup,
			InternalEntityIdFinder $idFinder ) {
		$this->queryInterface = $queryInterface;
		$this->schema = $schema;
		$this->propertyDataValueTypeLookup = $propertyDataValueTypeLookup;
		$this->idFinder = $idFinder;
	}

	/**
	 * Finds all entities that match the selection criteria.
	 * The matching entities are returned as an array of internal entity ids.
	 *
	 * TODO: return array of EntityId
	 *
	 * @since 0.1
	 *
	 * @param Description $description
	 * @param QueryOptions $options
	 *
	 * @return int[]
	 * @throws QueryNotSupportedException
	 */
	public function findMatchingEntities( Description $description, QueryOptions $options ) {
		if ( $description instanceof SomeProperty ) {
			return $this->findMatchingSomeProperty( $description, $options );
		}

		throw new QueryNotSupportedException( $description );
	}

	// TODO: this code needs some serious cleanup before it is extended
	protected function findMatchingSomeProperty( SomeProperty $description, QueryOptions $options ) {
		$propertyId = $description->getPropertyId();

		if ( !( $propertyId instanceof EntityId ) ) {
			throw new InvalidArgumentException( 'All property ids provided to the SQLStore should be EntityId objects' );
		}

		$dvHandler = $this->schema->getDataValueHandler(
			$this->propertyDataValueTypeLookup->getDataValueTypeForProperty( $propertyId ),
			SnakRole::MAIN_SNAK
		);

		$conditions = $this->getExtraConditions( $description, $dvHandler );

		$conditions['property_id'] = $this->getInternalId( $propertyId );

		$selectionResult = $this->queryInterface->select(
			$dvHandler->getDataValueTable()->getTableDefinition()->getName(),
			array(
				'subject_id',
			),
			$conditions
		);

		$entityIds = array();

		foreach ( $selectionResult as $resultRow ) {
			$entityIds[] = (int)$resultRow->subject_id;
		}

		return $entityIds;
	}

	protected function getInternalId( EntityId $id ) {
		return $this->idFinder->getInternalIdForEntity( $id );
	}

	protected function getExtraConditions( SomeProperty $description, DataValueHandler $dvHandler ) {
		$subDescription = $description->getSubDescription();

		if ( $subDescription instanceof ValueDescription ) {
			if ( $subDescription->getComparator() !== ValueDescription::COMP_EQUAL ) {
				throw new QueryNotSupportedException( $description );
			}

			return $dvHandler->getWhereConditions( $subDescription->getValue() );
		}

		return array();
	}

}

