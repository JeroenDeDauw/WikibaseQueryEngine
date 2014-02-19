<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\LatLongValue;
use InvalidArgumentException;
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
	 * @see DataValueHandler::newDataValueFromValueField
	 *
	 * @since 0.1
	 *
	 * @param string $valueFieldValue
	 *
	 * @return DataValue
	 */
	public function newDataValueFromValueField( $valueFieldValue ) {
		$value = json_decode( $valueFieldValue, true );
		return new LatLongValue( $value['latitude'], $value['longitude'] );
	}

	/**
	 * @see DataValueHandler::getWhereConditions
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public function getWhereConditions( DataValue $value ) {
		if ( !( $value instanceof LatLongValue ) ) {
			throw new InvalidArgumentException( 'Value is not a LatLongValue' );
		}

		return array(
			// Note: the code in this package is not dependent on MW.
			// So do not replace this with FormatJSON::encode.
			'value_json' => json_encode( $value->getArrayValue() ),
		);
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @since 0.1
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

		$values = array(
			'value_lat' => $value->getLatitude(),
			'value_lon' => $value->getLongitude(),

			// Note: the code in this package is not dependent on MW.
			// So do not replace this with FormatJSON::encode.
			'value_json' => json_encode( $value->getArrayValue() ),
		);

		return $values;
	}

}