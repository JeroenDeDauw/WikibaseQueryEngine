<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use Iterator;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\EntityIdParsingException;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * Simple query engine that works on top of the SQLStore.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DescriptionMatchFinder {

	private $connection;
	private $schema;
	private $propertyDataValueTypeLookup;
	private $idParser;

	public function __construct(
		Connection $connection,
		StoreSchema $schema,
		PropertyDataValueTypeLookup $propertyDataValueTypeLookup,
		EntityIdParser $idParser
	) {
		$this->connection = $connection;
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

	/**
	 * TODO: this code needs some serious cleanup before it is extended
	 *
	 * @param SomeProperty $description
	 * @param QueryOptions $options
	 *
	 * @return EntityId[]
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	private function findMatchingSomeProperty( SomeProperty $description, QueryOptions $options ) {
		$propertyId = $description->getPropertyId();

		if ( !( $propertyId instanceof EntityIdValue ) ) {
			throw new InvalidArgumentException( 'All property ids provided to the SQLStore should be EntityIdValue objects' );
		}

		$subDescription = $description->getSubDescription();

		if ( !( $subDescription instanceof ValueDescription ) ) {
			throw new QueryNotSupportedException( $description );
		}

		$queryBuilder = $this->createQueryBuilder( $propertyId->getEntityId(), $subDescription );

		$queryBuilder->setMaxResults( $options->getLimit() );
		$queryBuilder->setFirstResult( $options->getOffset() );

		return $this->getEntityIdsFromResult( $this->getResultFromQueryBuilder( $queryBuilder ) );
	}

	private function createQueryBuilder( PropertyId $propertyId, ValueDescription $description ) {
		$dvHandler = $this->schema->getDataValueHandlers()->getMainSnakHandler(
			$this->propertyDataValueTypeLookup->getDataValueTypeForProperty( $propertyId )
		);

		$queryBuilder = new QueryBuilder( $this->connection );

		$this->addFieldsToSelect(
			$queryBuilder,
			array( 'subject_id' ),
			$dvHandler
		);

		$queryBuilder->andWhere( $dvHandler->getTableName() . '.' . 'property_id= :property_id' );
		$queryBuilder->setParameter( ':property_id', $propertyId->getSerialization() );

		$dvHandler->addMatchConditions( $queryBuilder, $description );

		return $queryBuilder;
	}

	private function addFieldsToSelect( QueryBuilder $builder, array $fieldNames, DataValueHandler $dvHandler ) {
		foreach ( $fieldNames as $fieldName ) {
			$builder->select( $dvHandler->getTableName() . '.' . $fieldName );
		}

		$builder->from( $dvHandler->getTableName(), $dvHandler->getTableName() );
	}

	private function getResultFromQueryBuilder( QueryBuilder $builder ) {
		try {
			return $builder->execute();
		}
		catch ( DBALException $ex ) {
			throw new QueryEngineException( $ex->getMessage(), $ex->getCode(), $ex );
		}
	}

	/**
	 * @param Iterator|array[] $resultRows
	 *
	 * @return EntityId[]
	 */
	private function getEntityIdsFromResult( $resultRows ) {
		$entityIds = array();

		foreach ( $resultRows as $resultRow ) {
			try {
				$entityIds[] = $this->idParser->parse( $resultRow['subject_id'] );
			}
			catch ( EntityIdParsingException $ex ) {}
		}

		return $entityIds;
	}

}
