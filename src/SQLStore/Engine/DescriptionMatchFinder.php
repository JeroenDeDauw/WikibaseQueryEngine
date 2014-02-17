<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use InvalidArgumentException;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\SnakRole;

/**
 * Simple query engine that works on top of the SQLStore.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DescriptionMatchFinder {

	protected $queryInterface;
	protected $schema;
	protected $propertyDataValueTypeLookup;
	protected $idParser;

	public function __construct( QueryInterface $queryInterface,
			Schema $schema,
			PropertyDataValueTypeLookup $propertyDataValueTypeLookup, EntityIdParser $idParser ) {

		$this->queryInterface = $queryInterface;
		$this->schema = $schema;
		$this->propertyDataValueTypeLookup = $propertyDataValueTypeLookup;
		$this->idParser = $idParser;
	}

	/**
	 * Finds all entities that match the selection criteria.
	 * The matching entities are returned as an array of internal entity ids.
	 *
	 * @since 0.1
	 *
	 * @param Description $description
	 * @param QueryOptions $options
	 *
	 * @return EntityId[]
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

		if ( !( $propertyId instanceof EntityIdValue ) ) {
			throw new InvalidArgumentException( 'All property ids provided to the SQLStore should be EntityIdValue objects' );
		}

		$propertyId = $propertyId->getEntityId();

		$dvHandler = $this->schema->getDataValueHandler(
			$this->propertyDataValueTypeLookup->getDataValueTypeForProperty( $propertyId ),
			SnakRole::MAIN_SNAK
		);

		$conditions = $this->getExtraConditions( $description, $dvHandler );

		$conditions['property_id'] = $propertyId->getSerialization();

		$selectionResult = $this->queryInterface->select(
			$dvHandler->getDataValueTable()->getTableDefinition()->getName(),
			array(
				'entity_id',
			),
			$conditions
		);

		$entityIds = array();

		foreach ( $selectionResult as $resultRow ) {
			// TODO: handle parse exception
			$entityIds[] = $this->idParser->parse( $resultRow->entity_id );
		}

		return $entityIds;
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

