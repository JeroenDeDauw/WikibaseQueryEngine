<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\BooleanValue;
use DataValues\DataValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * Represents the mapping between BooleanValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BooleanHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 *
	 * @return string
	 */
	protected function getBaseTableName() {
		return 'boolean';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value', Type::BOOLEAN );

		$table->addIndex( array( 'value' ) );
	}

	/**
	 * @see DataValueHandler::getEqualityFieldName
	 *
	 * @return string
	 */
	public function getEqualityFieldName() {
		return 'value';
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof BooleanValue ) ) {
			throw new InvalidArgumentException( 'Value is not a BooleanValue.' );
		}

		$values = array(
			'value' => $value->getValue() ? 1 : 0,
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getEqualityFieldValue
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return int
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof BooleanValue ) ) {
			throw new InvalidArgumentException( 'Value is not a BooleanValue.' );
		}

		return $value->getValue() ? 1 : 0;
	}

}
