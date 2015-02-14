<?php

namespace Wikibase\QueryEngine\SQLStore\Engine\Interpreter;

use Ask\Language\Description\Description;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionInterpreter;
use Wikibase\QueryEngine\SQLStore\Engine\SqlQueryPart;

/**
 * @private
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SomePropertyInterpreter implements DescriptionInterpreter {

	private $queryBuilder;
	private $dvHandlerFetcher;

	public function __construct( QueryBuilder $queryBuilder, callable $dvHandlerFetcher ) {
		$this->queryBuilder = $queryBuilder;
		$this->dvHandlerFetcher = $dvHandlerFetcher;
	}

	/**
	 * @param Description $description
	 *
	 * @return boolean
	 */
	public function canInterpretDescription( Description $description ) {
		return $description instanceof SomeProperty;
	}

	/**
	 * @param Description $description
	 *
	 * @return SqlQueryPart
	 * @throws \InvalidArgumentException
	 */
	public function interpretDescription( Description $description ) {
		if ( !( $description instanceof SomeProperty ) ) {
			throw new InvalidArgumentException( 'Can only interpret SomeProperty descriptions' );
		}

		$subDescription = $description->getSubDescription();

		if ( !( $subDescription instanceof ValueDescription ) ) {
			throw new QueryNotSupportedException( $description );
		}

		$propertyId = $this->getPropertyIdFrom( $description );

		/**
		 * @var DataValueHandler $dvHandler
		 */
		$dvHandler = call_user_func( $this->dvHandlerFetcher, $propertyId );

		$this->queryBuilder->select( 'subject_id' )
			->from( $dvHandler->getTableName() )
			->orderBy( 'subject_id', 'ASC' );

		$this->queryBuilder->andWhere( 'property_id = :property_id' );
		$this->queryBuilder->setParameter( ':property_id', $propertyId->getSerialization() );

		$dvHandler->addMatchConditions( $this->queryBuilder, $subDescription );

		return new SqlQueryPart(); // TODO
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

}