<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\EntityIdHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers  Wikibase\QueryEngine\SQLStore\DVHandler\EntityIdHandler
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
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = [];

		$instances[] = new EntityIdHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @return EntityId[]
	 */
	protected function getValues() {
		$values = [];

		$values[] = new EntityIdValue( new ItemId( 'Q42' ) );
		$values[] = new EntityIdValue( new ItemId( 'Q9001' ) );
		$values[] = new EntityIdValue( new PropertyId( 'P23' ) );

		return $values;
	}

}
