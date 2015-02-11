<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\Engine;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\Tests\Integration\IntegrationStoreBuilder;

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
class PropertyValueMatchingTest extends DescriptionMatchingTestCase {

	public function testBothPropertyValueMatchesAreFound() {
		$this->insertManuallyConstructedItems();

		$description = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 1337 ) )
		);

		$expectedIds = array( new ItemId( 'Q1112' ), new ItemId( 'Q1115' ) );

		$this->assertDescriptionResultsInMatches( $description, $expectedIds );
	}

	public function testWhenPropertyMismatches_valueMatchesAreNotReturned() {
		$this->insertManuallyConstructedItems();

		$description = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P1' ) ),
			new ValueDescription( new NumberValue( 1337 ) )
		);

		$expectedIds = [];

		$this->assertDescriptionResultsInMatches( $description, $expectedIds );
	}

	public function testWhenValueMismatches_propertyMatchesAreNotReturned() {
		$this->insertManuallyConstructedItems();

		$description = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P43' ) ),
			new ValueDescription( new NumberValue( 1337 ) )
		);

		$expectedIds = array( new ItemId( 'Q1113' ) );

		$this->assertDescriptionResultsInMatches( $description, $expectedIds );
	}

	public function testWhenValueMismatches_propertyMatchesAreNotReturned2() {
		$this->insertManuallyConstructedItems();

		$description = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 72010 ) )
		);

		$expectedIds = array( new ItemId( 'Q1114' ) );

		$this->assertDescriptionResultsInMatches( $description, $expectedIds );
	}

}
