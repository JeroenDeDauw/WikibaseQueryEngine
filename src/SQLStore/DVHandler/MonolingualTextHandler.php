<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\MonolingualTextValue;
use InvalidArgumentException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;

/**
 * Represents the mapping between DataValues\MonolingualTextValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MonolingualTextHandler extends DataValueHandler {

	public function __construct() {
		parent::__construct( new DataValueTable(
			new TableDefinition(
				'mono_text',
				array(
					new FieldDefinition( 'value_text',
						TypeDefinition::TYPE_BLOB,
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_language',
						new TypeDefinition( TypeDefinition::TYPE_VARCHAR, 20 ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_json',
						TypeDefinition::TYPE_BLOB,
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value_json',
			'value_json',
			'value_text',
			'value_text'
		) );
	}

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
		return MonolingualTextValue::newFromArray( json_decode( $valueFieldValue, true ) );
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
		if ( !( $value instanceof MonolingualTextValue ) ) {
			throw new InvalidArgumentException( 'Value is not a MonolingualTextValue' );
		}

		$values = array(
			'value_text' => $value->getText(),
			'value_language' => $value->getLanguageCode(),

			'value_json' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getEqualityFieldValue
	 *
	 * @param DataValue $value
	 *
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof MonolingualTextValue ) ) {
			throw new InvalidArgumentException( 'Value is not a MonolingualTextValue' );
		}

		return json_encode( $value->getArrayValue() );
	}

}