<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\TimeValue;
use DataValues\TimeValueCalculator;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
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
		// For example: -1234567890123456-12-31T23:59:59+01:00
		$table->addColumn( 'value', Type::STRING, array( 'length' => 38 ) );
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
		return 'value';
	}

	/**
	 * @see DataValueHandler::getSortFieldNames
	 */
	public function getSortFieldNames() {
		return array( 'value_timestamp' );
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
		$precisionInSeconds = $calculator->getSecondsForPrecision( $value->getPrecision() );

		$before = $value->getBefore();
		// The range from before to after must be at least one unit long
		$after = max( 1, $value->getAfter() );

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
	 * @return string ISO date and time without leading plus and zeros,
	 * with the time zone in the format +01:00
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Value is not a TimeValue.' );
		}

		// This ignores leading plus and time zones on purpose, validation should not happen here
		if ( !preg_match( '/(-?\d+)(-\d\d-\d\dT\d\d:\d\d:\d\d)/', $value->getTime(), $matches ) ) {
			throw new InvalidArgumentException( 'Failed to parse time value ' . $value->getTime() . '.' );
		}
		list( , $year, $mmddhhmmss ) = $matches;

		$isoDateAndTime = sprintf( '%.0f', $year )
			. $mmddhhmmss
			. $this->getTimeZoneSuffix( $value->getTimezone() );

		return $isoDateAndTime;
	}

	/**
	 * @param int $minutes offset from UTC in minutes
	 *
	 * @return string time zone in the format +01:00 or Z for zero
	 */
	private function getTimeZoneSuffix( $minutes ) {
		if ( !$minutes ) {
			return 'Z';
		}

		return sprintf( '%+03d:%02d', intval( $minutes / 60 ), abs( $minutes ) % 60 );
	}

	/**
	 * @see DataValueHandler::addMatchConditions
	 *
	 * @param QueryBuilder $builder
	 * @param ValueDescription $description
	 *
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	public function addMatchConditions( QueryBuilder $builder, ValueDescription $description ) {
		// TODO
		throw new QueryNotSupportedException( $description, 'No query support implemented yet' );
	}

}
