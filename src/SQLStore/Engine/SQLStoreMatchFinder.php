<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Iterator;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\EntityIdParsingException;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\DescriptionMatchFinder;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\Engine\Interpreter\ConjunctionInterpreter;
use Wikibase\QueryEngine\SQLStore\Engine\Interpreter\SomePropertyInterpreter;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * Simple query engine that works on top of the SQLStore.
 *
 * @private
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
	 * @var DescriptionInterpreter[]
	 */
	private $descriptionInterpreters;

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

		$dataValueHandlerFetcher = function( PropertyId $propertyId ) {
			$dataTypeId = $this->propertyDataValueTypeLookup->getDataValueTypeForProperty( $propertyId );

			return $this->schema->getDataValueHandlers()->getMainSnakHandler( $dataTypeId );
		};

		$this->descriptionInterpreters = [
			new SomePropertyInterpreter( $this->queryBuilder, $dataValueHandlerFetcher ),
			new ConjunctionInterpreter()
		];

		$this->addOptions( $options );

		$this->getDescriptionInterpreter( $description )->interpretDescription( $description );

		return $this->getEntityIdsFromResult( $this->getResultFromQueryBuilder() );
	}

	private function getDescriptionInterpreter( Description $description ) {
		foreach ( $this->descriptionInterpreters as $interpreter ) {
			if ( $interpreter->canInterpretDescription( $description ) ) {
				return $interpreter;
			}
		}

		throw new QueryNotSupportedException( $description );
	}

	private function addOptions( QueryOptions $options ) {
		$this->queryBuilder->setMaxResults( $options->getLimit() );
		$this->queryBuilder->setFirstResult( $options->getOffset() );
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
		$entityIds = [];

		foreach ( $resultRows as $resultRow ) {
			try {
				$entityIds[] = $this->idParser->parse( $resultRow['subject_id'] );
			}
			catch ( EntityIdParsingException $ex ) {
				// TODO: log
			}
		}

		return $entityIds;
	}

}
