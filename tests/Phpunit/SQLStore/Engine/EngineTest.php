<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Ask\Language\Description\AnyValue;
use Ask\Language\Option\QueryOptions;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\SQLStore\Engine\Engine;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Engine\Engine
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
			new ItemId( 'Q1' ),
			new PropertyId( 'P2' ),
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
		$this->assertContainsOnlyInstancesOf( 'Wikibase\DataModel\Entity\EntityId', $entityIds );
		$this->assertEquals( $expectedIds, $entityIds );
	}

}
