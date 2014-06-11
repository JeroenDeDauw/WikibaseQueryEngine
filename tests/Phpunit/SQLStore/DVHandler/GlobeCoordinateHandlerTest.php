<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;
use OutOfRangeException;
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

	public function latLongProvider() {
		return array(
			array( -360, -360 ),
			array( -180, -180 ),
			array( -1, 0 ),
			array( 0, -1 ),
			array( 0, 0 ),
			array( 0, 1 ),
			array( 1, 0 ),
			array( 180, 180 ),
			array( 360, 360 ),
		);
	}

	/**
	 * @dataProvider latLongProvider
	 *
	 * @param float $latitude
	 * @param float $longitude
	 */
	public function testGetEqualityFieldValue_samePointAfter360Degree( $latitude, $longitude ) {
		$instance = $this->newInstance();

		$referenceCoordinate = new GlobeCoordinateValue(
			new LatLongValue( $latitude, $longitude ),
			null
		);
		$referenceHash = $instance->getEqualityFieldValue( $referenceCoordinate );

		foreach ( array( -360, 0, 360 ) as $latitudeDelta ) {
			foreach ( array( -360, 0, 360 ) as $longitudeDelta ) {
				try {
					$sameCoordinate = new GlobeCoordinateValue(
						new LatLongValue( $latitude + $latitudeDelta, $longitude + $longitudeDelta ),
						null
					);
					$sameHash = $instance->getEqualityFieldValue( $sameCoordinate );

					$this->assertEquals(
						$referenceHash,
						$sameHash,
						$sameCoordinate->getLatitude() . '|' . $sameCoordinate->getLongitude()
						. " is equal to $latitude|$longitude"
					);
				} catch ( OutOfRangeException $ex ) {
					// No need to test combinations that aren't accepted by LatLongValue
				}
			}
		}
	}

	public function testGetEqualityFieldValue_differentPointAfter180Degree() {
		$instance = $this->newInstance();

		$referenceCoordinate = new GlobeCoordinateValue(
			new LatLongValue( 0, 0 ),
			null
		);
		$differentCoordinate = new GlobeCoordinateValue(
			new LatLongValue( 0, 180 ),
			null
		);

		$this->assertNotEquals(
			$instance->getEqualityFieldValue( $referenceCoordinate ),
			$instance->getEqualityFieldValue( $differentCoordinate )
		);
	}

}
