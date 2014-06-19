<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\DecimalValue;
use DataValues\QuantityValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\QuantityHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\QuantityHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class QuantityHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$instances[] = new QuantityHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return QuantityValue[]
	 */
	protected function getValues() {
		$values = array();

		foreach ( array( 0, -1/3, 2/3, 99 ) as $amount ) {
			foreach ( array( '1', 'm' ) as $unit ) {
				foreach ( array( 0, 1/7, 7 ) as $upperDelta ) {
					foreach ( array( 0, -1/9, -9 ) as $lowerDelta ) {
						$values[] = new QuantityValue(
							new DecimalValue( $amount ),
							$unit,
							new DecimalValue( $amount + $upperDelta ),
							new DecimalValue( $amount + $lowerDelta )
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
	 * @param QuantityValue $quantityValue
	 */
	public function testGetInsertValues( QuantityValue $quantityValue ) {
		$instance = $this->newInstance();

		$insertValues = $instance->getInsertValues( $quantityValue );

		$this->assertInternalType( 'float', $insertValues['value_actual'] );
		$this->assertInternalType( 'float', $insertValues['value_lower_bound'] );
		$this->assertInternalType( 'float', $insertValues['value_upper_bound'] );
		$this->assertLessThanOrEqual( $insertValues['value_actual'], $insertValues['value_lower_bound'] );
		$this->assertGreaterThanOrEqual( $insertValues['value_actual'], $insertValues['value_upper_bound'] );
	}

}
