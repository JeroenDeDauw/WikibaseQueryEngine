<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\TimeValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
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
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$defaultHandlers = new DataValueHandlers();
		$instances[] = $defaultHandlers->getHandler( 'time' );

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

}
