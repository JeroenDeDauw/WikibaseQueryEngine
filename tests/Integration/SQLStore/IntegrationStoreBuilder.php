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
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @group medium
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IntegrationStoreBuilder {

	/**
	 * @param PHPUnit_Framework_TestCase $testCase
	 *
	 * @return SQLStoreWithDependencies
	 */
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
							new FieldDefinition(
								'value',
								new TypeDefinition( TypeDefinition::TYPE_FLOAT ),
								false
							),
							new FieldDefinition(
								'value_json',
								new TypeDefinition( TypeDefinition::TYPE_BLOB ),
								false
							),
						)
					),
					'value_json',
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

		return new SQLStoreWithDependencies(
			new SQLStore( $config ),
			$queryInterface,
			$tableBuilder,
			$definitionReader,
			$schemaModifier
		);
	}

}