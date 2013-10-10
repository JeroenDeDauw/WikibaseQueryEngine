<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

use PHPUnit_Framework_TestCase;
use Wikibase\Database\LazyDBConnectionProvider;
use Wikibase\Database\MediaWiki\MediaWikiQueryInterface;
use Wikibase\Database\MediaWiki\MediaWikiSchemaModifierBuilder;
use Wikibase\Database\MediaWiki\MWTableBuilderBuilder;
use Wikibase\Database\MediaWiki\MWTableDefinitionReaderBuilder;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\Store;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @group medium
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IntegrationStoreBuilder {

	public static function newStore( PHPUnit_Framework_TestCase $testCase ) {
		$dbConnectionProvider = new LazyDBConnectionProvider( DB_MASTER );

		$tbBuilder = new MWTableBuilderBuilder();
		$tableBuilder = $tbBuilder->setConnection( $dbConnectionProvider )->getTableBuilder();

		$queryInterface = new MediaWikiQueryInterface( $dbConnectionProvider );

		$drBuilder = new MWTableDefinitionReaderBuilder();
		$definitionReader = $drBuilder->setConnection( $dbConnectionProvider )
			->setQueryInterface( $queryInterface )->getTableDefinitionReader();

		$smBuilder = new MediaWikiSchemaModifierBuilder();
		$schemaModifier = $smBuilder->setConnection( $dbConnectionProvider )
			->setQueryInterface( $queryInterface )->getSchemaModifier();

		$config = new StoreConfig(
			'test_store',
			'integrationtest_',
			array(
				'number' => new NumberHandler( new DataValueTable(
					new TableDefinition(
						'number_table',
						array(
							new FieldDefinition( 'value', FieldDefinition::TYPE_FLOAT, false ),
							new FieldDefinition( 'json', FieldDefinition::TYPE_TEXT, false ),
						)
					),
					'json',
					'value',
					'value'
				) )
			)
		);

		$propertyDvTypeLookup = $testCase->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $testCase->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $testCase->returnValue( 'number' ) );

		$config->setPropertyDataValueTypeLookup( $propertyDvTypeLookup );

		return new Store( $config, $queryInterface, $tableBuilder, $definitionReader, $schemaModifier );
	}

}