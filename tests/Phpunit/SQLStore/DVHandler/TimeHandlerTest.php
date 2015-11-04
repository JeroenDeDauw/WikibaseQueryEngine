<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\TimeValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\TimeHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\TimeHandler
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
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = [];

		$instances[] = new TimeHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
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
				TimeValue::PRECISION_YEAR1G,
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
				TimeValue::PRECISION_YEAR10,
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

}
