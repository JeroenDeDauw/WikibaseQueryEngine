<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\LatLongValue;
use DataValues\TimeValue;
use InvalidArgumentException;
use RuntimeException;
use ValueParsers\CalendarModelParser;
use ValueParsers\TimeParser;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueTable;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland < jeroendedauw@gmail.com >
 */
class TimeHandler extends DataValueHandler {

	private $secondsPerYear = 31536000; // 365 * 24 * 60 * 60

	public function __construct() {
		parent::__construct( new DataValueTable(
			new TableDefinition(
				'time',
				array(
					new FieldDefinition(
						'value',
						new TypeDefinition( TypeDefinition::TYPE_VARCHAR, 33 ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_epoche',
						new TypeDefinition( TypeDefinition::TYPE_BIGINT ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_min_epoche',
						new TypeDefinition( TypeDefinition::TYPE_BIGINT ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_max_epoche',
						new TypeDefinition( TypeDefinition::TYPE_BIGINT ),
						FieldDefinition::NOT_NULL
					),
				),
				array(
					new IndexDefinition(
						'value_epoche',
						array( 'value_epoche' )
					),
					new IndexDefinition(
						'value_min_epoche',
						array( 'value_min_epoche' )
					),
					new IndexDefinition(
						'value_max_epoche',
						array( 'value_max_epoche' )
					),
				)
			),
			'value',
			'value_epoche',
			'value_epoche'
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

		$values = array(
			'value_epoche' => $this->getEpoche( $value->getTime() ),
			'value_epoche_min' => $this->getEpoche( $value->getTime() ),
			'value_epoche_max' => $this->getEpoche(
					$value->getTime(),
					$this->getSecondsFromPrecision( $value->getPrecision() )
				),

			'value' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @param string $timeString +0000000000002014-01-01T00:00:00Z
	 * @param int $secondsToAdd
	 *
	 * @throws RuntimeException
	 * @return int
	 */
	private function getEpoche( $timeString, $secondsToAdd = 0 ) {
		preg_match( '/^([-+]\d{1,16})-(.+Z)$/', $timeString, $matches );
		list(, $year, $timeWithoutYear ) = $matches;

		$timeWithoutYear = '01-' . $timeWithoutYear;
		$year = intval( $year );

		$epoche = strtotime( $timeWithoutYear );
		if( $epoche === false ){
			throw new RuntimeException( 'Failed to get epoche from time value: ' . $timeString );
		}

		return $epoche + ( $this->secondsPerYear * $year ) + $secondsToAdd;
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
				return 2592000; // 86400 * 30
			case TimeValue::PRECISION_YEAR:
				return 31536000; // 86400 * 365
			case TimeValue::PRECISION_10a:
				return 315360000;
			case TimeValue::PRECISION_100a:
				return 3153600000;
			case TimeValue::PRECISION_ka:
				return 31536000000;
			case TimeValue::PRECISION_10ka:
				return 315360000000;
			case TimeValue::PRECISION_100ka:
				return 3153600000000;
			case TimeValue::PRECISION_Ma:
				return 31536000000000;
			case TimeValue::PRECISION_10Ma:
				return 315360000000000;
			case TimeValue::PRECISION_100Ma:
				return 3153600000000000;
			case TimeValue::PRECISION_Ga:
				return 31536000000000000;
		}
		throw new RuntimeException( 'Unable to get seconds for precision:' . $precision );
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
			throw new InvalidArgumentException( 'Value is not a TimeValue' );
		}

		return $value->getTime() . '|' . $value->getPrecision() . '|' . $value->getCalendarModel();
	}

}