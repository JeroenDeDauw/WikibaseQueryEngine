<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\DVHandler;

use DataValues\GeoCoordinateValue;
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
	 * @return GeoCoordinateValue[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new GeoCoordinateValue( 0, 0 );
		$values[] = new GeoCoordinateValue( 23, 42 );
		$values[] = new GeoCoordinateValue( 2.3, 4.2, 9000.1 );
		$values[] = new GeoCoordinateValue( -2.3, -4.2, -9000.1, null, 'mars' );

		return $values;
	}

}
