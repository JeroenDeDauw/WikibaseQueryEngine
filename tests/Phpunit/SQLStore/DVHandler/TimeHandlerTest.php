<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\TimeValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\TimeHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\TimeHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @author Thiemo Mättig
 */
class TimeHandlerTest extends DataValueHandlerTest {

	/**
	 * @param string $time
	 * @param int $timezone in minutes
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getTimeValueMock( $time, $timezone = 0 ) {
		$timeValue = $this->getMockBuilder( 'DataValues\TimeValue' )
			->disableOriginalConstructor()
			->getMock();

		$timeValue->expects( $this->any() )
			->method( 'getTime' )
			->will( $this->returnValue( $time ) );
		$timeValue->expects( $this->any() )
			->method( 'getTimezone' )
			->will( $this->returnValue( $timezone ) );
		$timeValue->expects( $this->any() )
			->method( 'getPrecision' )
			->will( $this->returnValue( TimeValue::PRECISION_DAY ) );
		$timeValue->expects( $this->any() )
			->method( 'getCalendarModel' )
			->will( $this->returnValue( 'Stardate' ) );

		return $timeValue;
	}

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$instances[] = new TimeHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return TimeValue[]
	 */
	protected function getValues() {
		return array(
			new TimeValue(
				'+0000000000002014-01-01T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_DAY,
				'http://www.wikidata.org/entity/Q1985727'
			),
			new TimeValue(
				'+0000000000002014-06-00T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_MONTH,
				'http://www.wikidata.org/entity/Q1985727'
			),
			new TimeValue(
				'+1000000000000000-00-00T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_Ga,
				'http://www.wikidata.org/entity/Q1985727'
			),
			new TimeValue(
				'-0000000000000100-10-10T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_DAY,
				'http://www.wikidata.org/entity/Q1985727'
			),
			new TimeValue(
				'-0000000000000110-00-00T00:00:00Z',
				0, 0, 0,
				TimeValue::PRECISION_10a,
				'http://www.wikidata.org/entity/Q1985727'
			),
		);
	}

	/**
	 * @dataProvider epocheCalculationProvider
	 *
	 * @param string $time
	 * @param int $expected
	 * @param int $timezone in minutes
	 */
	public function testEpocheCalculation( $time, $expected, $timezone = 0 ) {
		$instance = $this->newInstance();

		$timeValue = $this->getTimeValueMock( $time, $timezone );
		$insertValues = $instance->getInsertValues( $timeValue );
		$epoche = $insertValues['value_epoche'];

		$this->assertEquals( $expected, $epoche );
	}

	public function epocheCalculationProvider() {
		return array(
			// Make sure it's identical to the PHP/Unix time stamps in current years
			array( '+2004-02-29T00:00:00Z', strtotime( '2004-02-29T00:00:00+00:00' ) ),
			array( '+2038-00-00T00:00:00Z', strtotime( '2038-01-01T00:00:00+00:00' ) ),

			// Time zones
			array( '+2000-01-01T12:59:59', strtotime( '2000-01-01T12:59:59-02:00' ), -120 ),
			array( '+2000-01-01T12:59:59', strtotime( '2000-01-01T12:59:59+04:45' ), 285 ),

			array( '+0401-00-00T00:00:00Z', -49512816000 ),
			array( '+1902-00-00T00:00:00Z', -2145916800 ),
			array( '+1939-00-00T00:00:00Z', -978307200 ),
			array( '+1969-12-31T23:59:00Z', -60 ),
			array( '+1969-12-31T23:59:59Z', -1 ),
			array( '+1970-01-01T00:00:00Z', 0 ),
			array( '+1970-01-01T00:00:01Z', 1 ),
			array( '+1970-01-01T00:01:00Z', 60 ),
			array( '+1972-02-29T00:00:00Z', 68169600 ),
			array( '+1996-02-29T00:00:00Z', 825552000 ),
			array( '+1999-12-31T23:59:59Z', 946684799 ),
			array( '+2000-01-01T00:00:00Z', 946684800 ),
			array( '+2000-02-01T00:00:00Z', 949363200 ),
			array( '+2000-02-29T00:00:00Z', 951782400 ),
			array( '+2001-00-00T00:00:00Z', 978307200 ),
			array( '+2001-01-01T00:00:00Z', 978307200 ),
			array( '+2014-04-30T12:35:55Z', 1398861355 ),
			array( '+2401-00-00T00:00:00Z', 13601088000 ),

			// Make sure there is only 1 second between these two
			array( '-0001-12-31T23:59:59Z', -62135596801 ),
			array( '+0001-00-00T00:00:00Z', -62135596800 ),

			// Year 0 does not exist, but we do not complain, assume -1
			array( '-0000-12-31T23:59:59Z', -62135596801 ),
			array( '+0000-00-00T00:00:00Z', floor( ( -1 - 1969 ) * 365.2425 ) * 86400 ),

			// Since there is no year 0, negative leap years are -1, -5 and so on
			array( '-8001-00-00T00:00:00Z', floor( ( -8001 - 1969 ) * 365.2425 ) * 86400 ),
			array( '-0005-00-00T00:00:00Z', floor( ( -5 - 1969 ) * 365.2425 ) * 86400 ),
			array( '+0004-00-00T00:00:00Z', floor( ( 4 - 1970 ) * 365.2425 ) * 86400 ),
			array( '+8000-00-00T00:00:00Z', floor( ( 8000 - 1970 ) * 365.2425 ) * 86400 ),

			// PHP_INT_MIN is -2147483648
			array( '-2147484001-00-00T00:00:00Z', floor( ( -2147484001 - 1969 ) * 365.2425 ) * 86400 ),
			// PHP_INT_MAX is +2147483647
			array( '+2147484000-00-00T00:00:00Z', floor( ( 2147484000 - 1970 ) * 365.2425 ) * 86400 ),
		);
	}

	public function testEqualityFieldValue() {
		$instance = $this->newInstance();

		$timeValue = $this->getTimeValueMock( '41153.7' );
		$equalityFieldValue = $instance->getEqualityFieldValue( $timeValue );

		$this->assertEquals( '41153.7|11|Stardate', $equalityFieldValue );
	}

}
