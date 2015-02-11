<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\StringValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler
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
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = [];

		$instances[] = new StringHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @return StringValue[]
	 */
	protected function getValues() {
		$values = [];

		$values[] = new StringValue( 'foo' );
		$values[] = new StringValue( '' );
		$values[] = new StringValue( ' foo ' );
		$values[] = new StringValue( ' foo bar baz bah! hax ~=[,,_,,]:3' );

		return $values;
	}

}
