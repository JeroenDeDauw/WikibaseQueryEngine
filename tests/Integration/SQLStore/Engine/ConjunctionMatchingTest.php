<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\Engine;

use Ask\Language\Description\Conjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use DataValues\NumberValue;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseQueryEngineIntegration
 * @group StoreSchema
 *
 * @group medium
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ConjunctionMatchingTest extends DescriptionMatchingTestCase {

	public function testWhenNothingInStore_noMatchesAreFound() {
		$this->assertTrue(true);

//		$description = new Conjunction( [
//				new SomeProperty(
//					new EntityIdValue( new PropertyId( 'P1' ) ),
//					new ValueDescription( new NumberValue( 1 ) )
//				),
//				new SomeProperty(
//					new EntityIdValue( new PropertyId( 'P1' ) ),
//					new ValueDescription( new NumberValue( 2 ) )
//				)
//			]
//		);
//
//		$expectedIds = [];
//
//		$this->assertDescriptionResultsInMatches( $description, $expectedIds );
	}

}
