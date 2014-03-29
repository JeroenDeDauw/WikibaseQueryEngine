<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\StringValue;
use InvalidArgumentException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\StringHasher;

/**
 * Represents the mapping between Wikibase\StringValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StringHandler extends DataValueHandler {

	/**
	 * @var StringHasher
	 */
	private $stringHasher;

	public function __construct() {
		parent::__construct( new DataValueTable(
			new TableDefinition(
				'string',
				array(
					new FieldDefinition( 'value',
						new TypeDefinition( TypeDefinition::TYPE_VARCHAR ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition( 'hash',
						new TypeDefinition( TypeDefinition::TYPE_VARCHAR, 70 ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value',
			'hash',
			'hash',
			'value'
		) );

		$this->stringHasher = new StringHasher();
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
		return new StringValue( $valueFieldValue );
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
		if ( !( $value instanceof StringValue ) ) {
			throw new InvalidArgumentException( 'Value is not a StringValue' );
		}

		$values = array(
			'value' => $value->getValue(),
			'hash' => $this->stringHasher->hash( $value->getValue() ),
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
		if ( !( $value instanceof StringValue ) ) {
			throw new InvalidArgumentException( 'Value is not a StringValue' );
		}

		return $value->getValue();
	}

}