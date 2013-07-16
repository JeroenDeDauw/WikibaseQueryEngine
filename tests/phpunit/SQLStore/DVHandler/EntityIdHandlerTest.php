<?php

namespace Wikibase\QueryEngine\Tests\SQLStore\DVHandler;

use Wikibase\EntityId;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\Tests\SQLStore\DataValueHandlerTest;

/**
 * @covers  Wikibase\QueryEngine\SQLStore\DVHandler\EntityIdHandler
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
class EntityIdHandlerTest extends DataValueHandlerTest {

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
		$instances[] = $defaultHandlers->getHandler( 'wikibase-entityid' );

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return EntityId[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new EntityId( 'item', 42 );
		$values[] = new EntityId( 'item', 9001 );
		$values[] = new EntityId( 'property', 23 );
		$values[] = new EntityId( 'query', 7201010 );

		return $values;
	}

}
