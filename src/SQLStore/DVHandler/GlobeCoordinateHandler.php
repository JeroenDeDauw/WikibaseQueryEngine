<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValueCalculator;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class GlobeCoordinateHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 */
	protected function getBaseTableName() {
		return 'globecoordinate';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value_globe',   Type::STRING, array( 'length' => 255 ) );
		$table->addColumn( 'value_lat',     Type::DECIMAL );
		$table->addColumn( 'value_lon',     Type::DECIMAL );
		$table->addColumn( 'value_min_lat', Type::DECIMAL );
		$table->addColumn( 'value_max_lat', Type::DECIMAL );
		$table->addColumn( 'value_min_lon', Type::DECIMAL );
		$table->addColumn( 'value_max_lon', Type::DECIMAL );
		$table->addColumn( 'hash',          Type::STRING, array( 'length' => 32 ) );

		// We need to search for greater/lower than. This can't use a combined index.
		$table->addIndex( array( 'value_lat' ) );
		$table->addIndex( array( 'value_lon' ) );
		$table->addIndex( array( 'value_min_lat' ) );
		$table->addIndex( array( 'value_max_lat' ) );
		$table->addIndex( array( 'value_min_lon' ) );
		$table->addIndex( array( 'value_max_lon' ) );
	}

	/**
	 * @see DataValueHandler::getSortFieldNames
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
		if ( !( $value instanceof GlobeCoordinateValue ) ) {
			throw new InvalidArgumentException( 'Value is not a GlobeCoordinateValue' );
		}

		$calculator = new LatLongValueCalculator();
		$latitude = $value->getLatitude();
		$longitude = $value->getLongitude();
		$precision = abs( $value->getPrecision() );

		$values = array(
			'value_globe'   => $this->normalizeGlobe( $value->getGlobe() ),
			'value_lat'     => $calculator->normalize( $latitude ),
			'value_lon'     => $calculator->normalize( $longitude ),
			'value_min_lat' => $calculator->normalize( $latitude - $precision ),
			'value_max_lat' => $calculator->normalize( $latitude + $precision ),
			'value_min_lon' => $calculator->normalize( $longitude - $precision ),
			'value_max_lon' => $calculator->normalize( $longitude + $precision ),

			// No special human-readable hash needed, everything required is in the other fields.
			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @todo This method could be moved to the calculator.
	 *
	 * @param string $globe
	 *
	 * @return string
	 */
	private function normalizeGlobe( $globe ) {
		if ( !is_string( $globe ) || $globe === '' ) {
			return GlobeCoordinateValue::GLOBE_EARTH;
		}

		return $globe;
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

		if ( !( $value instanceof GlobeCoordinateValue ) ) {
			throw new InvalidArgumentException( 'Value is not a GlobeCoordinateValue' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			$calculator = new LatLongValueCalculator();
			$latitude = $value->getLatitude();
			$longitude = $value->getLongitude();
			$precision = abs( $value->getPrecision() );

			$builder->andWhere( $this->getTableName() . '.value_globe = :globe' );
			$builder->andWhere( $this->getTableName() . '.value_lat >= :min_lat' );
			$builder->andWhere( $this->getTableName() . '.value_lat <= :max_lat' );
			$builder->andWhere( $this->getTableName() . '.value_lon <= :min_lon' );
			$builder->andWhere( $this->getTableName() . '.value_lon <= :max_lon' );
			$builder->setParameter( ':globe', $value->getGlobe() );
			$builder->setParameter( ':min_lat', $calculator->normalize( $latitude - $precision ) );
			$builder->setParameter( ':max_lat', $calculator->normalize( $latitude + $precision ) );
			$builder->setParameter( ':min_lon', $calculator->normalize( $longitude - $precision ) );
			$builder->setParameter( ':max_lon', $calculator->normalize( $longitude + $precision ) );
		} else {
			throw new QueryNotSupportedException( $description, 'Only equality is supported' );
		}
	}

}
