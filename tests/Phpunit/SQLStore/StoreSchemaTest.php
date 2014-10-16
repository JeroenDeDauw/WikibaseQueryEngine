<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @covers Wikibase\QueryEngine\SQLStore\StoreSchema
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreSchemaTest extends \PHPUnit_Framework_TestCase {

	public function testGetTables() {
		$handlers = new DataValueHandlers();
		$handlers->addMainSnakHandler( 'string', new StringHandler() );

		$schema = new StoreSchema( 'foo', $handlers );

		$tables = $schema->getTables();

		$this->assertInternalType( 'array', $tables );
		$this->assertContainsOnlyInstancesOf( 'Doctrine\DBAL\Schema\Table', $tables );

		$tableCount = count( $tables );

		$handlers = new DataValueHandlers();
		$handlers->addMainSnakHandler( 'string', new StringHandler() );
		$handlers->addMainSnakHandler( 'number', new NumberHandler() );
		$handlers->addQualifierHandler( 'number', new NumberHandler() );

		$schema = new StoreSchema( 'foo', $handlers );

		$tables = $schema->getTables();

		$this->assertInternalType( 'array', $tables );
		$this->assertContainsOnlyInstancesOf( 'Doctrine\DBAL\Schema\Table', $tables );

		$this->assertCount( $tableCount + 2, $tables );
	}

}
