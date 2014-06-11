<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\GlobeCoordinateValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
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

		$table->addIndex( array( 'value_lon', 'value_lat' ) );
		$table->addIndex( array( 'value_min_lon', 'value_min_lat' ) );
		$table->addIndex( array( 'value_max_lon', 'value_max_lat' ) );
	}

	/**
	 * @see DataValueHandler::getValueFieldName
	 */
	public function getValueFieldName() {
		// FIXME: This needs to be an array.
		return 'value_lat';
	}

	/**
	 * @see DataValueHandler::getEqualityFieldName
	 */
	public function getEqualityFieldName() {
		return 'hash';
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

		$precision = abs( $value->getPrecision() );
		$values = array(
			'value_globe'   => $value->getGlobe(),
			'value_lat'     => $value->getLatitude(),
			'value_lon'     => $value->getLongitude(),
			'value_min_lat' => $value->getLatitude() - $precision,
			'value_max_lat' => $value->getLatitude() + $precision,
			'value_min_lon' => $value->getLongitude() - $precision,
			'value_max_lon' => $value->getLongitude() + $precision,

			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof GlobeCoordinateValue ) ) {
			throw new InvalidArgumentException( 'Value is not a GlobeCoordinateValue' );
		}

		$serialization = ( fmod( $value->getLatitude() + 540, 360 ) - 180 ) . '|'
			. ( fmod( $value->getLongitude() + 540, 360 ) - 180 ) . '|'
			. $value->getGlobe();
		return md5( $serialization );
	}

}
