<?php

namespace Wikibase\QueryEngine\Tests\SQLStore;

use Ask\Language\Description\AnyValue;
use Ask\Language\Option\QueryOptions;
use Wikibase\EntityId;
use Wikibase\QueryEngine\SQLStore\Engine\Engine;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Engine\Engine
 *
 * @file
 * @since 0.1
 *
 * @ingroup WikibaseQueryEngineTest
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EngineTest extends \PHPUnit_Framework_TestCase {

	public function testGetMatchingEntities() {
		$description = new AnyValue();
		$options = new QueryOptions( 42, 10 );
		$expectedIds = array(
			new EntityId( 'item', 1 ),
			new EntityId( 'property', 2 ),
			new EntityId( 'foo', 123 ),
		);

		$matchFinder = $this->getMockBuilder( 'Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder' )
			->disableOriginalConstructor()->getMock();

		$matchFinder->expects( $this->once() )
			->method( 'findMatchingEntities' )
			->with(
				$this->equalTo( $description ),
				$this->equalTo( $options )
			)
			->will( $this->returnValue( $expectedIds ) );

		$engine = new Engine( $matchFinder );

		$entityIds = $engine->getMatchingEntities( $description, $options );

		$this->assertInternalType( 'array', $entityIds );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\EntityId', $entityIds );
		$this->assertEquals( $expectedIds, $entityIds );
	}

}
