<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\NumberValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class NumberHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$instances[] = new NumberHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @return NumberValue[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new NumberValue( 0 );
		$values[] = new NumberValue( 1 );
		$values[] = new NumberValue( 7101010 );
		$values[] = new NumberValue( 9000.1 );
		$values[] = new NumberValue( 0.000042 );
		$values[] = new NumberValue( -0.000042 );
		$values[] = new NumberValue( -123456 );
		$values[] = new NumberValue( 71010.101010 );

		return $values;
	}

	/**
	 * @dataProvider valueProvider
	 *
	 * @param NumberValue $numberValue
	 */
	public function testGetInsertValues( NumberValue $numberValue ) {
		$instance = $this->newInstance();

		$insertValues = $instance->getInsertValues( $numberValue );

		$this->assertInternalType( 'numeric', $insertValues['value'] );
	}

}
