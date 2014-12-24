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
use Wikibase\QueryEngine\DescriptionMatchFinder;
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
class SQLStoreMatchFinder implements DescriptionMatchFinder {

	private $connection;
	private $schema;
	private $propertyDataValueTypeLookup;
	private $idParser;

	/**
	 * @var QueryBuilder
	 */
	private $queryBuilder;

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
	 * @param Description $description
	 * @param QueryOptions $options
	 *
	 * @return EntityId[]
	 * @throws QueryNotSupportedException
	 */
	public function getMatchingEntities( Description $description, QueryOptions $options ) {
		$this->queryBuilder = new QueryBuilder( $this->connection );

		$this->addOptions( $options );

		if ( $description instanceof SomeProperty ) {
			return $this->findMatchingSomeProperty( $description );
		}

		throw new QueryNotSupportedException( $description );
	}

	private function addOptions( QueryOptions $options ) {
		$this->queryBuilder->setMaxResults( $options->getLimit() );
		$this->queryBuilder->setFirstResult( $options->getOffset() );
	}

	/**
	 * @param SomeProperty $description
	 *
	 * @return EntityId[]
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	private function findMatchingSomeProperty( SomeProperty $description ) {
		$subDescription = $description->getSubDescription();

		if ( !( $subDescription instanceof ValueDescription ) ) {
			throw new QueryNotSupportedException( $description );
		}

		$this->addPropertyAndValueDescription(
			$this->getPropertyIdFrom( $description ),
			$subDescription
		);

		return $this->getEntityIdsFromResult( $this->getResultFromQueryBuilder() );
	}

	/**
	 * @param SomeProperty $description
	 *
	 * @throws InvalidArgumentException
	 * @return PropertyId
	 */
	private function getPropertyIdFrom( SomeProperty $description ) {
		$propertyId = $description->getPropertyId();

		if ( !( $propertyId instanceof EntityIdValue ) ) {
			throw new InvalidArgumentException( 'All property ids provided to the SQLStore should be EntityIdValue objects' );
		}

		return $propertyId->getEntityId();
	}

	private function addPropertyAndValueDescription( PropertyId $propertyId, ValueDescription $description ) {
		$dvHandler = $this->getDataValueHandlerFor( $propertyId );

		$this->queryBuilder->select( 'subject_id' )
			->from( $dvHandler->getTableName() )
			->orderBy( 'subject_id', 'ASC' );

		$this->queryBuilder->andWhere( 'property_id = :property_id' );
		$this->queryBuilder->setParameter( ':property_id', $propertyId->getSerialization() );

		$dvHandler->addMatchConditions( $this->queryBuilder, $description );
	}

	private function getDataValueHandlerFor( PropertyId $propertyId ) {
		$dataTypeId = $this->propertyDataValueTypeLookup->getDataValueTypeForProperty( $propertyId );

		return $this->schema->getDataValueHandlers()->getMainSnakHandler( $dataTypeId );
	}

	private function getResultFromQueryBuilder() {
		try {
			return $this->queryBuilder->execute();
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
			catch ( EntityIdParsingException $ex ) {
				// Reporting invalid IDs would not be helpful at this point, just skip them.
			}
		}

		return $entityIds;
	}

}
