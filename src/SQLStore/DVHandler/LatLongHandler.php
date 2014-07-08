<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\GlobeMath;
use DataValues\LatLongValue;
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
		$table->addColumn( 'hash',      Type::STRING, array( 'length' => 32 ) );

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
		if ( !( $value instanceof LatLongValue ) ) {
			throw new InvalidArgumentException( 'Value is not a LatLongValue.' );
		}

		$math = new GlobeMath();
		$normalized = $math->normalizeLatLong( $value, self::MINIMUM_LONGITUDE );

		$values = array(
			'value_lat' => $normalized->getLatitude(),
			'value_lon' => $normalized->getLongitude(),

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

		if ( !( $value instanceof LatLongValue ) ) {
			throw new InvalidArgumentException( 'Value is not a LatLongValue.' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			$this->addInRangeConditions( $builder, $value );
		} else {
			parent::addMatchConditions( $builder, $description );
		}
	}

	/**
	 * @param QueryBuilder $builder
	 * @param LatLongValue $value
	 */
	private function addInRangeConditions( QueryBuilder $builder, LatLongValue $value ) {
		$math = new GlobeMath();
		$normalized = $math->normalizeLatLong( $value, self::MINIMUM_LONGITUDE );
		$lat = $normalized->getLatitude();
		$lon = $normalized->getLongitude();

		$builder->andWhere( $this->getTableName() . '.value_lat >= :min_lat' );
		$builder->andWhere( $this->getTableName() . '.value_lat <= :max_lat' );
		$builder->andWhere( $this->getTableName() . '.value_lon >= :min_lon' );
		$builder->andWhere( $this->getTableName() . '.value_lon <= :max_lon' );

		$builder->setParameter( ':min_lat', $lat - self::EPSILON );
		$builder->setParameter( ':max_lat', $lat + self::EPSILON );
		$builder->setParameter( ':min_lon', $lon - self::EPSILON );
		$builder->setParameter( ':max_lon', $lon + self::EPSILON );
	}

}
