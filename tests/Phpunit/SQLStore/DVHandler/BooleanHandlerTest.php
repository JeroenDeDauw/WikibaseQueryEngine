<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\BooleanValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\BooleanHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\BooleanHandler
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BooleanHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = [];

		$instances[] = new BooleanHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @return BooleanValue[]
	 */
	protected function getValues() {
		$values = [];

		$values[] = new BooleanValue( true );
		$values[] = new BooleanValue( false );

		return $values;
	}

}
