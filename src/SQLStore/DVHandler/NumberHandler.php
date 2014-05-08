<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\NumberValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;

/**
 * Represents the mapping between Wikibase\NumberValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NumberHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 */
	protected function getBaseTableName() {
		return 'number';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value', Type::DECIMAL );
	}

	/**
	 * @see DataValueHandler::getValueFieldName
	 */
	public function getValueFieldName() {
		return 'value';
	}

	/**
	 * @see DataValueHandler::newDataValueFromValueField
	 *
	 * @param string $valueFieldValue
	 *
	 * @return DataValue
	 */
	public function newDataValueFromValueField( $valueFieldValue ) {
		return new NumberValue( $valueFieldValue );
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
		if ( !( $value instanceof NumberValue ) ) {
			throw new InvalidArgumentException( 'Value is not a NumberValue' );
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
		if ( !( $value instanceof NumberValue ) ) {
			throw new InvalidArgumentException( 'Value is not a NumberValue' );
		}

		return $value->getValue();
	}




}