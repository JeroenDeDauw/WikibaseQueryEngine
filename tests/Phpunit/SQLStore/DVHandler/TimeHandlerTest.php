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
 */
class TimeHandlerTest extends DataValueHandlerTest {

	/**
	 * @param string $time
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getTimeValueMock( $time ) {
		$timeValue = $this->getMockBuilder( 'DataValues\TimeValue' )
			->disableOriginalConstructor()
			->getMock();

		$timeValue->expects( $this->any() )
			->method( 'getTime' )
			->will( $this->returnValue( $time ) );
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
		$values = array();

		$values[] = new TimeValue(
			'+0000000000002014-01-01T00:00:00Z',
			0, 0, 0,
			TimeValue::PRECISION_DAY,
			'http://www.wikidata.org/entity/Q1985727'
		);
		$values[] = new TimeValue(
			'+0000000000002014-06-00T00:00:00Z',
			0, 0, 0,
			TimeValue::PRECISION_MONTH,
			'http://www.wikidata.org/entity/Q1985727'
		);
		$values[] = new TimeValue(
			'+1000000000000000-00-00T00:00:00Z',
			0, 0, 0,
			TimeValue::PRECISION_Ga,
			'http://www.wikidata.org/entity/Q1985727'
		);
		$values[] = new TimeValue(
			'-0000000000000100-10-10T00:00:00Z',
			0, 0, 0,
			TimeValue::PRECISION_DAY,
			'http://www.wikidata.org/entity/Q1985727'
		);
		$values[] = new TimeValue(
			'-0000000000000110-00-00T00:00:00Z',
			0, 0, 0,
			TimeValue::PRECISION_10a,
			'http://www.wikidata.org/entity/Q1985727'
		);

		return $values;
	}

	/**
	 * @dataProvider epocheCalculationProvider
	 *
	 * @param string $time
	 * @param int $expected
	 */
	public function testEpocheCalculation( $time, $expected ) {
		$instance = $this->newInstance();

		$timeValue = $this->getTimeValueMock( $time );
		$insertValues = $instance->getInsertValues( $timeValue );
		$epoche = $insertValues['value_epoche'];

		$this->assertEquals( $expected, $epoche );
	}

	public function epocheCalculationProvider() {
		return array(
			array( '+0000000000000401-00-00T00:00:00Z', -49512816000 ),
			array( '+0000000000001902-00-00T00:00:00Z', -2145916800 ),
			array( '+0000000000001939-00-00T00:00:00Z', -978307200 ),
			array( '+0000000000001969-12-31T23:59:00Z', -60 ),
			array( '+0000000000001969-12-31T23:59:59Z', -1 ),
			array( '+0000000000001970-01-01T00:00:00Z', 0 ),
			array( '+0000000000001970-01-01T00:00:01Z', 1 ),
			array( '+0000000000001970-01-01T00:01:00Z', 60 ),
			array( '+0000000000001972-02-29T00:00:00Z', 68169600 ),
			array( '+0000000000001996-02-29T00:00:00Z', 825552000 ),
			array( '+0000000000001999-12-31T23:59:59Z', 946684799 ),
			array( '+0000000000002000-01-01T00:00:00Z', 946684800 ),
			array( '+0000000000002000-02-01T00:00:00Z', 949363200 ),
			array( '+0000000000002000-02-29T00:00:00Z', 951782400 ),
			array( '+0000000000002001-00-00T00:00:00Z', 978307200 ),
			array( '+0000000000002001-01-01T00:00:00Z', 978307200 ),
			array( '+0000000000002004-02-29T00:00:00Z', 1078012800 ),
			array( '+0000000000002014-04-30T12:35:55Z', 1398861355 ),
			array( '+0000000000002038-00-00T00:00:00Z', 2145916800 ),
			array( '+0000000000002401-00-00T00:00:00Z', 13601088000 ),
		);
	}

	public function testEqualityFieldValue() {
		$instance = $this->newInstance();

		$timeValue = $this->getTimeValueMock( '41153.7' );
		$equalityFieldValue = $instance->getEqualityFieldValue( $timeValue );

		$this->assertEquals( '41153.7|11|Stardate', $equalityFieldValue );
	}

}
