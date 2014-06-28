<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;
use DataValues\LatLongValueCalculator;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * Represents the mapping between DataValues\LatLongValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LatLongHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 *
	 * @return string
	 */
	protected function getBaseTableName() {
		return 'latlong';
	}

	/**
	 * @see DataValueHandler::completeTable
	 *
	 * @param Table $table
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value_lat', Type::FLOAT );
		$table->addColumn( 'value_lon', Type::FLOAT );
		$table->addColumn( 'hash', Type::STRING, array( 'length' => 32 ) );

		// We need to search for greater/lower than. This can't use a combined index.
		$table->addIndex( array( 'value_lat' ) );
		$table->addIndex( array( 'value_lon' ) );
	}

	/**
	 * @see DataValueHandler::getSortFieldNames
	 *
	 * @return string[]
	 */
	public function getSortFieldNames() {
		// Order by West-East first
		return array( 'value_lon', 'value_lat' );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @param DataValue $value
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof LatLongValue ) ) {
			throw new InvalidArgumentException( 'Value is not a LatLongValue' );
		}

		$calculator = new LatLongValueCalculator();

		$values = array(
			'value_lat' => $calculator->normalize( $value->getLatitude() ),
			'value_lon' => $calculator->normalize( $value->getLongitude() ),

			// No special human-readable hash needed, everything required is in the other fields.
			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::addMatchConditions
	 *
	 * @param QueryBuilder $builder
	 * @param ValueDescription $description
	 *
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	public function addMatchConditions( QueryBuilder $builder, ValueDescription $description ) {
		$value = $description->getValue();

		// Need a GlobeCoordinateValue to search for LatLongValues because of the precision.
		if ( !( $value instanceof GlobeCoordinateValue ) ) {
			throw new InvalidArgumentException( 'Value is not a GlobeCoordinateValue' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			$calculator = new LatLongValueCalculator();
			$latitude = $value->getLatitude();
			$longitude = $value->getLongitude();
			$precision = abs( $value->getPrecision() );

			$builder->andWhere( $this->getTableName() . '.value_lat >= :min_lat' );
			$builder->andWhere( $this->getTableName() . '.value_lat <= :max_lat' );
			$builder->andWhere( $this->getTableName() . '.value_lon <= :min_lon' );
			$builder->andWhere( $this->getTableName() . '.value_lon <= :max_lon' );
			$builder->setParameter( ':min_lat', $calculator->normalize( $latitude - $precision ) );
			$builder->setParameter( ':max_lat', $calculator->normalize( $latitude + $precision ) );
			$builder->setParameter( ':min_lon', $calculator->normalize( $longitude - $precision ) );
			$builder->setParameter( ':max_lon', $calculator->normalize( $longitude + $precision ) );
		} else {
			throw new QueryNotSupportedException( $description, 'Only equality is supported' );
		}
	}

}
