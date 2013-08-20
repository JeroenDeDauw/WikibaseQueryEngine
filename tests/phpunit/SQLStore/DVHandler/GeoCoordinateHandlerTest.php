<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\DVHandler;

use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\Tests\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\GeoCoordinateHandler
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoCoordinateHandlerTest extends DataValueHandlerTest {

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
		$instances[] = $defaultHandlers->getHandler( 'globecoordinate' );

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return GlobeCoordinateValue[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new GlobeCoordinateValue( new LatLongValue( 0, 0 ), 1 );
		$values[] = new GlobeCoordinateValue( new LatLongValue( 23, 42 ), 0.1 );
		$values[] = new GlobeCoordinateValue( new LatLongValue( 2.3, 4.2 ), 10 );
		$values[] = new GlobeCoordinateValue( new LatLongValue( -2.3, -4.2 ), 1, 'mars' );

		return $values;
	}

}
