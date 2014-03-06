<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use InvalidArgumentException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;

/**
 * Represents the mapping between Wikibase\EntityId and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityIdHandler extends DataValueHandler {

	public function __construct() {
		parent::__construct( new DataValueTable(
			new TableDefinition(
				'entityid',
				array(
					new FieldDefinition(
						'value_id',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_type',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value_id',
			'value_id',
			'value_id',
			'value_id'
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
		$parser = new BasicEntityIdParser(); // TODO: inject
		return new EntityIdValue( $parser->parse( $valueFieldValue ) ); // TODO: handle parse exception
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
		if ( !( $value instanceof EntityIdValue ) ) {
			throw new InvalidArgumentException( '$value is not a EntityIdValue' );
		}

		$values = array(
			'value_id' => $value->getEntityId()->getSerialization(),
			'value_type' => $value->getEntityId()->getEntityType(),
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
		if ( !( $value instanceof EntityIdValue ) ) {
			throw new InvalidArgumentException( '$value is not a EntityIdValue' );
		}

		return $value->getEntityId()->getSerialization();
	}

}