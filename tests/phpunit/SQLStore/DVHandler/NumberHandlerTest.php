<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\DVHandler;

use DataValues\NumberValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\Tests\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler
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
class NumberHandlerTest extends DataValueHandlerTest {

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
		$instances[] = $defaultHandlers->getHandler( 'number' );

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
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

}
