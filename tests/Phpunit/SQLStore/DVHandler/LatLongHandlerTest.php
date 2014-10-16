<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\Geo\Values\LatLongValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\LatLongHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\LatLongHandler
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class LatLongHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$instances[] = new LatLongHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
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

	/**
	 * @dataProvider valueProvider
	 *
	 * @param LatLongValue $value
	 */
	public function testGetInsertValues( LatLongValue $value ) {
		$instance = $this->newInstance();

		$insertValues = $instance->getInsertValues( $value );

		$this->assertInternalType( 'float', $insertValues['value_lat'] );
		$this->assertInternalType( 'float', $insertValues['value_lon'] );
	}

}
