<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

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

	/**
	 * @see DataValueHandler::getBaseTableName
	 *
	 * @return string
	 */
	protected function getBaseTableName() {
		return 'entityid';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		// Same length as MediaWiki titles.
		$table->addColumn( 'value_id', Type::STRING, array( 'length' => 255 ) );
		// Same length as in the Wikibase tables.
		$table->addColumn( 'value_type', Type::STRING, array( 'length' => 32 ) );

		$table->addIndex( array( 'value_id' ) );
		$table->addIndex( array( 'value_type' ) );
	}

	/**
	 * @see DataValueHandler::getEqualityFieldName
	 *
	 * @return string
	 */
	public function getEqualityFieldName() {
		return 'value_id';
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
