<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DataValueHandlersBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCallingGetHandlersAfterConstructionGivesNoHandlers() {
		$builder = new DataValueHandlersBuilder();

		$this->assertSame( array(), $builder->getHandlers()->getMainSnakHandlers() );
		$this->assertSame( array(), $builder->getHandlers()->getQualifierHandlers() );
	}

	public function testCallingWithSimpleHandlersAddsStringHandler() {
		$builder = new DataValueHandlersBuilder();
		$handlers = $builder->withSimpleHandlers()->getHandlers();

		$this->assertArrayHasKey( 'string', $handlers->getMainSnakHandlers() );
		$this->assertArrayHasKey( 'string', $handlers->getQualifierHandlers() );
	}

}
