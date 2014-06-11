<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\GlobeCoordinateHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\GlobeCoordinateHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class GlobeCoordinateHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$instances[] = new GlobeCoordinateHandler();

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

		foreach ( array( 0, -1/3, 2/3, 99 ) as $latitude ) {
			foreach ( array( 0, -1/3, 2/3, 99 ) as $longitude ) {
				foreach ( array( null, 0, 0.01/3, -1 ) as $precision ) {
					foreach ( array( GlobeCoordinateValue::GLOBE_EARTH, 'Vulcan' ) as $globe ) {
						$values[] = new GlobeCoordinateValue(
							new LatLongValue( $latitude, $longitude ),
							$precision,
							$globe
						);
					}
				}
			}
		}

		return $values;
	}

	/**
	 * @dataProvider valueProvider
	 *
	 * @param GlobeCoordinateValue $globeCoordinateValue
	 */
	public function testGetInsertValues( GlobeCoordinateValue $globeCoordinateValue ) {
		$instance = $this->newInstance();

		$insertValues = $instance->getInsertValues( $globeCoordinateValue );

		$this->assertInternalType( 'string', $insertValues['value_globe'] );
		$this->assertNotEmpty( $insertValues['value_globe'] );
		$this->assertInternalType( 'float', $insertValues['value_lat'] );
		$this->assertInternalType( 'float', $insertValues['value_lon'] );
		$this->assertInternalType( 'float', $insertValues['value_min_lat'] );
		$this->assertInternalType( 'float', $insertValues['value_max_lat'] );
		$this->assertInternalType( 'float', $insertValues['value_min_lon'] );
		$this->assertInternalType( 'float', $insertValues['value_max_lon'] );
		$this->assertLessThanOrEqual( $insertValues['value_lat'], $insertValues['value_min_lat'] );
		$this->assertGreaterThanOrEqual( $insertValues['value_lat'], $insertValues['value_max_lat'] );
		$this->assertLessThanOrEqual( $insertValues['value_lon'], $insertValues['value_min_lon'] );
		$this->assertGreaterThanOrEqual( $insertValues['value_lon'], $insertValues['value_max_lon'] );
	}

}
