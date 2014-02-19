<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\BooleanValue;
use DataValues\DataValue;
use InvalidArgumentException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;

/**
 * Represents the mapping between Wikibase\BooleanValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BooleanHandler extends DataValueHandler {

	public function __construct() {
		parent::__construct( new DataValueTable(
			new TableDefinition(
				'boolean',
				array(
					new FieldDefinition(
						'value',
						new TypeDefinition( TypeDefinition::TYPE_TINYINT ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value',
			'value',
			'value'
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
		return new BooleanValue( $valueFieldValue );
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
		if ( !( $value instanceof BooleanValue ) ) {
			throw new InvalidArgumentException( 'Value is not a BooleanValue' );
		}

		$values = array(
			'value' => $value->getValue(),
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
		if ( !( $value instanceof BooleanValue ) ) {
			throw new InvalidArgumentException( 'Value is not a BooleanValue' );
		}

		return $value->getValue();
	}

}