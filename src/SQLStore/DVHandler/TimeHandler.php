<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\LatLongValue;
use DataValues\TimeValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use RuntimeException;
use ValueParsers\CalendarModelParser;
use ValueParsers\TimeParser;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland < jeroendedauw@gmail.com >
 */
class TimeHandler extends DataValueHandler {

	/**
	 * Average length of a year in the Gregorian calendar.
	 * 365 + 1 / 4 - 1 / 100 + 1 / 400 = 365.2425 days.
	 */
	const SECONDS_PER_YEAR = 31556952;

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
		$table->addColumn( 'value_epoche', Type::BIGINT );
		$table->addColumn( 'value_epoche_min', Type::BIGINT );
		$table->addColumn( 'value_epoche_max', Type::BIGINT );
		$table->addIndex( array( 'value_epoche' ) );
		$table->addIndex( array( 'value_epoche_min' ) );
		$table->addIndex( array( 'value_epoche_max' ) );
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
		return 'value_epoche';
	}

	/**
	 * @see DataValueHandler::getSortFieldName
	 */
	public function getSortFieldName() {
		return 'value_epoche';
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
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Value is not a TimeValue' );
		}

		$epoche = $this->getEpoche( $value->getTime() );
		// XXX: Shouldn't min/max use the after/before values?
		$values = array(
			'value_epoche'     => $epoche,
			'value_epoche_min' => $epoche,
			'value_epoche_max' => $epoche
				+ $this->getSecondsFromPrecision( $value->getPrecision() ),

			'value' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @param string $time
	 *
	 * @throws RuntimeException
	 * @return int
	 */
	private function getEpoche( $time ) {
		// Validation is done in TimeValue. As long if we found enough numbers we are fine.
		if ( !preg_match( '/([-+]?\d+)\D+(\d+)\D+(\d+)\D+(\d+)\D+(\d+)\D+(\d+)/', $time, $matches )
		) {
			throw new RuntimeException( "Failed to parse time value $time." );
		}
		list( , $fullYear, $month, $day, $hour, $minute, $second ) = $matches;

		// We use mktime only for the month, day and time calculation. Set the year to the smallest
		// possible in the 1970-2038 range to be safe, even if it's 1901-2038 since PHP 5.1.0.
		$year = $this->isLeapYear( $fullYear ) ? 1972 : 1970;

		$defaultTimezone = date_default_timezone_get();
		date_default_timezone_set( 'UTC' );
		// With day/month set to 0 mktime would calculate the last day of the previous month/year.
		// In the context of this calculation we must assume 0 means "start of the month/year".
		$timestamp = mktime( $hour, $minute, $second, max( 1, $month ), max( 1, $day ), $year );
		date_default_timezone_set( $defaultTimezone );

		if ( $timestamp === false ) {
			throw new RuntimeException( "Failed to get epoche from time value $time." );
		}

		$missingYears = $fullYear - $year;
		$missingLeapDays = $this->getLeapDays( $fullYear ) - $this->getLeapDays( $year );

		return $timestamp + ( $missingYears * 365 + $missingLeapDays ) * 86400;
	}

	/**
	 * @param int $year
	 *
	 * @return bool
	 */
	private function isLeapYear( $year ) {
		$isMultipleOf4   = $year %   4 === 0;
		$isMultipleOf100 = $year % 100 === 0;
		$isMultipleOf400 = $year % 400 === 0;
		return $isMultipleOf4 && !$isMultipleOf100 || $isMultipleOf400;
	}

	/**
	 * @param int $year
	 *
	 * @return int
	 */
	private function getLeapDays( $year ) {
		$year = abs( $year );
		return (int)( $year / 4 ) - (int)( $year / 100 ) + (int)( $year / 400 );
	}

	/**
	 * @param int $precision
	 *
	 * @throws RuntimeException
	 * @return int
	 */
	private function getSecondsFromPrecision( $precision ) {
		switch ( $precision ) {
			case TimeValue::PRECISION_SECOND:
				return 1;
			case TimeValue::PRECISION_MINUTE:
				return 60;
			case TimeValue::PRECISION_HOUR:
				return 3600;
			case TimeValue::PRECISION_DAY:
				return 86400;
			case TimeValue::PRECISION_MONTH:
				return self::SECONDS_PER_YEAR / 12;
			case TimeValue::PRECISION_YEAR:
				return self::SECONDS_PER_YEAR;
			case TimeValue::PRECISION_10a:
				return self::SECONDS_PER_YEAR * 10;
			case TimeValue::PRECISION_100a:
				return self::SECONDS_PER_YEAR * 100;
			case TimeValue::PRECISION_ka:
				return self::SECONDS_PER_YEAR * 1000;
			case TimeValue::PRECISION_10ka:
				return self::SECONDS_PER_YEAR * 10000;
			case TimeValue::PRECISION_100ka:
				return self::SECONDS_PER_YEAR * 100000;
			case TimeValue::PRECISION_Ma:
				return self::SECONDS_PER_YEAR * 1000000;
			case TimeValue::PRECISION_10Ma:
				return self::SECONDS_PER_YEAR * 10000000;
			case TimeValue::PRECISION_100Ma:
				return self::SECONDS_PER_YEAR * 100000000;
			case TimeValue::PRECISION_Ga:
				return self::SECONDS_PER_YEAR * 1000000000;
		}

		throw new RuntimeException( "Unable to get seconds for precision $precision." );
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
		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Value is not a TimeValue.' );
		}

		return $value->getTime() . '|' . $value->getPrecision() . '|' . $value->getCalendarModel();
	}

}
