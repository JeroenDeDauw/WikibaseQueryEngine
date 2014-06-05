<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\TimeValue;
use DataValues\TimeValueCalculator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class TimeHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 */
	protected function getBaseTableName() {
		return 'time';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value', Type::STRING, array( 'length' => 33 ) );
		$table->addColumn( 'value_timestamp', Type::BIGINT );
		$table->addColumn( 'value_min_timestamp', Type::BIGINT );
		$table->addColumn( 'value_max_timestamp', Type::BIGINT );

		$table->addIndex( array( 'value_timestamp' ) );
		$table->addIndex( array( 'value_min_timestamp' ) );
		$table->addIndex( array( 'value_max_timestamp' ) );
	}

	/**
	 * @see DataValueHandler::getValueFieldName
	 */
	public function getValueFieldName() {
		return 'value';
	}

	/**
	 * @see DataValueHandler::getEqualityFieldName
	 */
	public function getEqualityFieldName() {
		return 'value_timestamp';
	}

	/**
	 * @see DataValueHandler::getSortFieldName
	 */
	public function getSortFieldName() {
		return 'value_timestamp';
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
		$value = explode( '|', $valueFieldValue, 3 );
		return new TimeValue( $value[0], 0, 0, 0, (int)$value[1], $value[2] );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Value is not a TimeValue' );
		}

		$calculator = new TimeValueCalculator();
		$timestamp = $calculator->getTimestamp( $value );
		$before = $value->getBefore();
		// The range from before to after must be at least one unit long
		$after = max( 1, $value->getAfter() );
		$precisionInSeconds = $calculator->getSecondsForPrecision( $value->getPrecision() );
		$values = array(
			'value_timestamp' => $timestamp,
			'value_min_timestamp' => $timestamp - $before * $precisionInSeconds,
			'value_max_timestamp' => $timestamp + $after * $precisionInSeconds,

			'value' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getEqualityFieldValue
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Value is not a TimeValue.' );
		}

		return $value->getTime() . '|' . $value->getPrecision() . '|' . $value->getCalendarModel();
	}

}
