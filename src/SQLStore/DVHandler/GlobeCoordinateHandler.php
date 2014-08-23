<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\GlobeCoordinateValue;
use DataValues\GlobeMath;
use DataValues\LatLongValue;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.3
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class GlobeCoordinateHandler extends DataValueHandler {

	/**
	 * @var GlobeMath
	 */
	private $math;

	public function __construct() {
		$this->math = new GlobeMath();
	}

	/**
	 * @see DataValueHandler::getBaseTableName
	 *
	 * @return string
	 */
	protected function getBaseTableName() {
		return 'globecoordinate';
	}

	/**
	 * @see DataValueHandler::completeTable
	 *
	 * @param Table $table
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value_globe',   Type::STRING, array( 'length' => 255, 'notnull' => false ) );
		$table->addColumn( 'value_lat',     Type::FLOAT );
		$table->addColumn( 'value_lon',     Type::FLOAT );
		$table->addColumn( 'value_min_lat', Type::FLOAT );
		$table->addColumn( 'value_max_lat', Type::FLOAT );
		$table->addColumn( 'value_min_lon', Type::FLOAT );
		$table->addColumn( 'value_max_lon', Type::FLOAT );
		$table->addColumn( 'hash',          Type::STRING, array( 'length' => 32 ) );

		// TODO: We still need to find out if combined indexes are better or not.
		$table->addIndex( array( 'value_lon', 'value_lat' ) );
		$table->addIndex( array( 'value_min_lat', 'value_max_lat', 'value_min_lon', 'value_max_lon' ) );
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
		if ( !( $value instanceof GlobeCoordinateValue ) ) {
			throw new InvalidArgumentException( 'Value is not a GlobeCoordinateValue.' );
		}

		$normalized = $this->math->normalizeGlobeCoordinate( $value );
		$lat = $normalized->getLatitude();
		$lon = $normalized->getLongitude();
		$precision = abs( $value->getPrecision() );

		$values = array(
			'value_globe'   => $this->normalizeGlobe( $value->getGlobe() ),
			'value_lat'     => $lat,
			'value_lon'     => $lon,
			'value_min_lat' => $lat - $precision,
			'value_max_lat' => $lat + $precision,
			'value_min_lon' => $lon - $precision,
			'value_max_lon' => $lon + $precision,

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

		if ( !( $value instanceof GlobeCoordinateValue ) ) {
			throw new InvalidArgumentException( 'Value is not a GlobeCoordinateValue.' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			$this->addInRangeConditions( $builder, $value );
		} else {
			parent::addMatchConditions( $builder, $description );
		}
	}

	/**
	 * @param QueryBuilder $builder
	 * @param GlobeCoordinateValue $value
	 */
	private function addInRangeConditions( QueryBuilder $builder, GlobeCoordinateValue $value ) {
		$value = $this->math->normalizeGlobeCoordinate( $value );
		$lat = $value->getLatitude();
		$lon = $value->getLongitude();
		$epsilon = abs( $value->getPrecision() );

		$builder->andWhere( $this->getTableName() . '.value_globe = :globe' );
		$builder->andWhere( $this->getTableName() . '.value_lat >= :min_lat' );
		$builder->andWhere( $this->getTableName() . '.value_lat <= :max_lat' );
		$builder->andWhere( $this->getTableName() . '.value_lon >= :min_lon' );
		$builder->andWhere( $this->getTableName() . '.value_lon <= :max_lon' );

		$builder->setParameter( ':globe', $this->normalizeGlobe( $value->getGlobe() ) );
		$builder->setParameter( ':min_lat', $lat - $epsilon );
		$builder->setParameter( ':max_lat', $lat + $epsilon );
		$builder->setParameter( ':min_lon', $lon - $epsilon );
		$builder->setParameter( ':max_lon', $lon + $epsilon );
	}

	/**
	 * @param string $globe
	 *
	 * @return string|null
	 */
	private function normalizeGlobe( $globe ) {
		$globe = $this->math->normalizeGlobe( $globe );

		if ( $globe === GlobeCoordinateValue::GLOBE_EARTH ) {
			$globe = null;
		}

		return $globe;
	}

}
