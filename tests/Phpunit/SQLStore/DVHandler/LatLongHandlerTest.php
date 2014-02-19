<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\LatLongHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LatLongHandlerTest extends DataValueHandlerTest {

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
		$instances[] = $defaultHandlers->getHandler( 'latlong' );

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return LatLongValue[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new LatLongValue( 0, 0 );
		$values[] = new LatLongValue( 23, 42 );
		$values[] = new LatLongValue( 2.3, 4.2 );
		$values[] = new LatLongValue( -2.3, -4.2 );

		return $values;
	}

}
