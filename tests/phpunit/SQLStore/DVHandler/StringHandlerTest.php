<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\DVHandler;

use DataValues\StringValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\Tests\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler
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
class StringHandlerTest extends DataValueHandlerTest {

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
		$instances[] = $defaultHandlers->getHandler( 'string' );

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return StringValue[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new StringValue( 'foo' );
		$values[] = new StringValue( '' );
		$values[] = new StringValue( ' foo ' );
		$values[] = new StringValue( ' foo bar baz bah! hax ~=[,,_,,]:3' );

		return $values;
	}

}
