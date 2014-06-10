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

	public function amountProvider() {
		return array(
			array( '1', 1.0 ),

			// Separate with space to avoid confusion
			array( '1 m', 1.0, 'm' ),

			// Rely on PHP`s build-in significant digits limitation
			array( '0.33333333333333', 1/3 ),
			array( '3.3333333333333E-9', 0.00000001/3 ),
		);
	}

	/**
	 * @dataProvider amountProvider
	 *
	 * @param string $expected
	 * @param float $amount
	 * @param string $unit
	 */
	public function testGetEqualityFieldValue( $expected, $amount, $unit = '1' ) {
		$instance = $this->newInstance();

		$amount = new DecimalValue( $amount );
		$quantityValue = new QuantityValue(
			$amount,
			$unit,
			$amount,
			$amount
		);
		$equalityFieldValue = $instance->getEqualityFieldValue( $quantityValue );

		$this->assertEquals( $expected, $equalityFieldValue );
	}

}
