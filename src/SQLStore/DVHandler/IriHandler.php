<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\IriValue;
use InvalidArgumentException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;

/**
 * Represents the mapping between DataValues\IriValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IriHandler extends DataValueHandler {

	public function __construct() {
		parent::__construct( new DataValueTable(
			new TableDefinition(
				'iri',
				array(
					new FieldDefinition(
						'value_scheme',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_fragment',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_query',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_hierp',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),

					new FieldDefinition(
						'value_iri',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_json',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value_json',
			'value_json',
			'value_iri',
			'value_iri'
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
		return IriValue::newFromArray( json_decode( $valueFieldValue, true ) );
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
			'value_scheme' => $value->getScheme(),
			'value_fragment' => $value->getFragment(),
			'value_query' => $value->getQuery(),
			'value_hierp' => $value->getHierarchicalPart(),

			'value_iri' => $value->getValue(),

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
		if ( !( $value instanceof IriValue ) ) {
			throw new InvalidArgumentException( 'Value is not a IriValue' );
		}

		return json_encode( $value->getArrayValue() );
	}

}