<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\IriValue;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * Represents the mapping between DataValues\IriValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IriHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::newDataValueFromValueField
	 *
	 * @since 0.1
	 *
	 * @param $valueFieldValue // TODO: mixed or string?
	 *
	 * @return DataValue
	 */
	public function newDataValueFromValueField( $valueFieldValue ) {
		return IriValue::newFromArray( json_decode( $valueFieldValue, true ) );
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
		if ( !( $value instanceof IriValue ) ) {
			throw new InvalidArgumentException( 'Value is not a IriValue' );
		}

		return array(
			// Note: the code in this package is not dependent on MW.
			// So do not replace this with FormatJSON::encode.
			'json' => json_encode( $value->getArrayValue() ),
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
		if ( !( $value instanceof IriValue ) ) {
			throw new InvalidArgumentException( 'Value is not a IriValue' );
		}

		$values = array(
			'scheme' => $value->getScheme(),
			'fragment' => $value->getFragment(),
			'query' => $value->getQuery(),
			'hierp' => $value->getHierarchicalPart(),

			'iri' => $value->getValue(),

			// Note: the code in this package is not dependent on MW.
			// So do not replace this with FormatJSON::encode.
			'json' => json_encode( $value->getArrayValue() ),
		);

		return $values;
	}

}