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
	 * @dataProvider valueProvider
	 *
	 * @param TimeValue $timeValue
	 */
	public function testGetInsertValues( TimeValue $timeValue ) {
		$instance = $this->newInstance();

		$insertValues = $instance->getInsertValues( $timeValue );

		$this->assertInternalType( 'float', $insertValues['value_timestamp'] );
		$this->assertInternalType( 'float', $insertValues['value_min_timestamp'] );
		$this->assertInternalType( 'float', $insertValues['value_max_timestamp'] );
		$this->assertLessThanOrEqual( $insertValues['value_timestamp'], $insertValues['value_min_timestamp'] );
		$this->assertGreaterThan( $insertValues['value_timestamp'], $insertValues['value_max_timestamp'] );
	}

	public function isoTimeProvider() {
		return array(
			// Strip plus sign and leading zeros
			array( '2001-02-03T04:05:06Z', '+00002001-02-03T04:05:06Z' ),

			// Attach actual time zone if not zero
			array( '2001-02-03T04:05:06+00:59', '+00002001-02-03T04:05:06Z', 59 ),
			array( '2001-02-03T04:05:06+01:00', '+00002001-02-03T04:05:06Z', 60 ),
			array( '2001-02-03T04:05:06-01:01', '+00002001-02-03T04:05:06Z', -61 ),
			array( '2001-02-03T04:05:06-100:00', '+00002001-02-03T04:05:06Z', -6000 ),

			// Keep minus sign on negative years
			array( '-2001-02-03T04:05:06Z', '-00002001-02-03T04:05:06Z' ),

			// No four digit years
			array( '1-02-03T04:05:06Z', '+00000001-02-03T04:05:06Z' ),
			array( '-1-02-03T04:05:06Z', '-00000001-02-03T04:05:06Z' ),

			// Make sure 32 bit integer clipping does not happen
			array( '100000000000000-02-03T04:05:06Z', '+100000000000000-02-03T04:05:06Z' ),
			array( '-100000000000000-02-03T04:05:06Z', '-100000000000000-02-03T04:05:06Z' ),
		);
	}

	/**
	 * @dataProvider isoTimeProvider
	 *
	 * @param string $expectedIsoTime
	 * @param string $time an ISO 8601 date and time
	 * @param int $timezone offset from UTC in minutes
	 */
	public function testGetEqualityFieldValue( $expectedIsoTime, $time, $timezone = 0 ) {
		$instance = $this->newInstance();

		$timeValue = new TimeValue(
			$time,
			$timezone,
			0,
			0,
			TimeValue::PRECISION_SECOND,
			'http://www.wikidata.org/entity/Q1985727'
		);
		$equalityFieldValue = $instance->getEqualityFieldValue( $timeValue );

		$this->assertEquals( $expectedIsoTime, $equalityFieldValue );
	}

}
