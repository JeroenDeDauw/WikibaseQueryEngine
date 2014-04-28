<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

use PDO;
use PHPUnit_Framework_TestCase;
use Wikibase\Database\MySQL\MySQLTableDefinitionReader;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\Database\PrefixingTableNameFormatter;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IntegrationStoreBuilder {

	const DB_NAME = 'qengine_tests';

	/**
	 * @param PHPUnit_Framework_TestCase $testCase
	 *
	 * @return SQLStoreWithDependencies
	 */
	public static function newStore( PHPUnit_Framework_TestCase $testCase ) {
		$builder = new self( $testCase );
		return $builder->buildStore();
	}

	private $testCase;

	private function __construct( PHPUnit_Framework_TestCase $testCase ) {
		$this->testCase = $testCase;
	}

	private function buildStore() {
		$factory = new PDOFactory( $this->newPDO() );
		$tableBuilder = $factory->newMySQLTableBuilder( self::DB_NAME );
		$queryInterface = $factory->newMySQLQueryInterface();

		return new SQLStoreWithDependencies(
			new SQLStore( $this->newStoreConfig() ),
			$queryInterface,
			$tableBuilder,
			$this->newTableDefinitionReader( $queryInterface ),
			$factory->newMySQLSchemaModifier()
		);
	}

	private function newPDO() {
		try {
			return new PDO(
				'mysql:dbname=' . self::DB_NAME . ';host=localhost',
				'qengine_tester',
				'mysql_is_evil'
			);
		}
		catch ( \PDOException $ex ) {
			$this->testCase->markTestSkipped(
				'Test not run, presumably the database is not set up: ' . $ex->getMessage()
			);
		}
	}

	private function newStoreConfig() {
		$config = new StoreConfig(
			'QueryR Replicator QueryEngine',
			'qr_',
			array(
				'number' => new NumberHandler()
			)
		);

		$config->setPropertyDataValueTypeLookup( $this->newDataValueTypeLookupStub() );

		return $config;
	}

	private function newDataValueTypeLookupStub() {
		$propertyDvTypeLookup = $this->testCase->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $this->testCase->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->testCase->returnValue( 'number' ) );

		return $propertyDvTypeLookup;
	}

	private function newTableDefinitionReader( QueryInterface $queryInterface ) {
		return new MySQLTableDefinitionReader(
			$queryInterface,
			new PrefixingTableNameFormatter( 'prefix_' )
		);
	}

}