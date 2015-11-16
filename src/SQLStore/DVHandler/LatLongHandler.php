<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\Geo\GlobeMath;
use DataValues\Geo\Values\LatLongValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\WhereConditions;

/**
 * Represents the mapping between LatLongValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class LatLongHandler extends DataValueHandler {

	/**
	 * Default to the Earth/Moon longitude range
	 */
	const MINIMUM_LONGITUDE = -180;

	/**
	 * Default to approximately a second (1/3600) for range searches. This is an arbitrary
	 * decision because coordinates like 12°34'56" with a precision of a second are very common.
	 */
	const EPSILON = 0.00028;

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

		// TODO: We still need to find out if combined indexes are better or not.
		$table->addIndex( array( 'value_lon', 'value_lat' ) );
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
		if ( $value instanceof LatLongValue ) {
			$normalized = $this->math->normalizeLatLong( $value, self::MINIMUM_LONGITUDE );
		} else {
			throw new InvalidArgumentException( 'Value is not a LatLongValue.' );
		}

		$values = array(
			'value_lat' => $normalized->getLatitude(),
			'value_lon' => $normalized->getLongitude(),

			// No special human-readable hash needed, everything required is in the other fields.
			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getWhereConditions
	 *
	 * @param ValueDescription $description
	 *
	 * @return WhereConditions
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	public function getWhereConditions( ValueDescription $description ) {
		$value = $description->getValue();

		if ( $value instanceof LatLongValue ) {
			$epsilon = self::EPSILON;
			$value = $this->math->normalizeLatLong( $value, self::MINIMUM_LONGITUDE );
		} else {
			throw new InvalidArgumentException( 'Value is not a LatLongValue.' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			return $this->getInRangeConditions( $value, $epsilon );
		}

		return parent::getWhereConditions( $description );
	}

	/**
	 * @param LatLongValue $value
	 * @param float|int $epsilon
	 * @return WhereConditions
	 */
	private function getInRangeConditions( LatLongValue $value, $epsilon ) {
		$conditions = new WhereConditions();

		$lat = $value->getLatitude();
		$lon = $value->getLongitude();

		$conditions->addCondition( 'value_lat >= :min_lat' );
		$conditions->addCondition( 'value_lat <= :max_lat' );
		$conditions->addCondition( 'value_lon >= :min_lon' );
		$conditions->addCondition( 'value_lon <= :max_lon' );

		$conditions->setParameter( ':min_lat', $lat - $epsilon );
		$conditions->setParameter( ':max_lat', $lat + $epsilon );
		$conditions->setParameter( ':min_lon', $lon - $epsilon );
		$conditions->setParameter( ':max_lon', $lon + $epsilon );

		return $conditions;
	}

}
