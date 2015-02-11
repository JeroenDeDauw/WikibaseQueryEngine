<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DataValueHandlers
 * @uses Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler
 * @uses Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DataValueHandlersTest extends \PHPUnit_Framework_TestCase {

	public function testNewDataValueHandlersIsEmpty() {
		$handlers = new DataValueHandlers();

		$this->assertEquals( [], $handlers->getMainSnakHandlers() );
		$this->assertEquals( [], $handlers->getQualifierHandlers() );
	}

	public function testAddingMainSnakHandlers() {
		$handlers = new DataValueHandlers();

		$handlers->addMainSnakHandler( 'number', new NumberHandler() );
		$handlers->addMainSnakHandler( 'string', new StringHandler() );

		$this->assertEquals(
			new NumberHandler(),
			$handlers->getMainSnakHandler( 'number' )
		);

		$this->assertEquals(
			new StringHandler(),
			$handlers->getMainSnakHandler( 'string' )
		);

		$this->assertEquals(
			array(
				'number' => new NumberHandler(),
				'string' => new StringHandler(),
			),
			$handlers->getMainSnakHandlers()
		);
	}

	public function testAddingQualifierHandlers() {
		$handlers = new DataValueHandlers();

		$handlers->addQualifierHandler( 'number', new NumberHandler() );
		$handlers->addQualifierHandler( 'string', new StringHandler() );

		$this->assertEquals(
			new NumberHandler(),
			$handlers->getQualifierHandler( 'number' )
		);

		$this->assertEquals(
			new StringHandler(),
			$handlers->getQualifierHandler( 'string' )
		);

		$this->assertEquals(
			array(
				'number' => new NumberHandler(),
				'string' => new StringHandler(),
			),
			$handlers->getQualifierHandlers()
		);
	}

	public function testTableNamePrefixing() {
		$handlers = new DataValueHandlers();

		$handlers->addQualifierHandler( 'number', new NumberHandler() );
		$handlers->addQualifierHandler( 'string', new StringHandler() );

		$handlers->getQualifierHandler( 'number' )->setTablePrefix( 'qualifier_' );
		$handlers->getQualifierHandler( 'string' )->setTablePrefix( 'qualifier_' );

		$this->assertEquals(
			'qualifier_number',
			$handlers->getQualifierHandler( 'number' )->getTableName()
		);

		$this->assertEquals(
			'qualifier_string',
			$handlers->getQualifierHandler( 'string' )->getTableName()
		);
	}

}
