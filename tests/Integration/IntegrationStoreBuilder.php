<?php

namespace Wikibase\QueryEngine\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use PHPUnit_Framework_TestCase;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IntegrationStoreBuilder {

	const DB_NAME = 'qengine_tests';

	/**
	 * @var PHPUnit_Framework_TestCase
	 */
	private $testCase;

	/**
	 * @param PHPUnit_Framework_TestCase $testCase
	 *
	 * @return SQLStoreWithDependencies
	 */
	public static function newStore( PHPUnit_Framework_TestCase $testCase ) {
		$builder = new self( $testCase );
		return $builder->buildStore();
	}

	private function __construct( PHPUnit_Framework_TestCase $testCase ) {
		$this->testCase = $testCase;
	}

	private function buildStore() {
		$handlersBuilder = new DataValueHandlersBuilder();

		return new SQLStoreWithDependencies(
			new SQLStore(
				new StoreSchema(
					'qr_',
					$handlersBuilder->withSimpleHandlers()->getHandlers()
				),
				new StoreConfig( 'QueryEngine integration test store' )
			),
			$this->newConnection(),
			$this->getPropertyDataValueTypeLookup(),
			new BasicEntityIdParser()
		);
	}

	/**
	 * @return PropertyDataValueTypeLookup
	 */
	private function getPropertyDataValueTypeLookup() {
		$propertyDvTypeLookup = $this->testCase->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $this->testCase->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->testCase->returnValue( 'number' ) );

		return $propertyDvTypeLookup;
	}

	private function newConnection() {
		if ( !isset( $GLOBALS['db_type'] ) ) {
			return DriverManager::getConnection( array(
				'driver' => 'pdo_sqlite',
				'memory' => true,
			) );
		}

		$this->recreateDatabase();

		return DriverManager::getConnection( $this->getConnectionParams() );
	}

	private function recreateDatabase() {
		$realConn = DriverManager::getConnection( $this->getConnectionParams() );
		$dbName = $realConn->getDatabase();
		$realConn->close();

		if ( $GLOBALS['db_type'] === 'pdo_pgsql' ) {
			$this->recreatePostgresDatabase( $dbName );
		}
		else {
			$this->recreateSaneDatabase( $dbName );
		}
	}

	private function recreateSaneDatabase( $dbName ) {
		// Connect to tmpdb in order to drop and create the real test db.
		$tmpConn = DriverManager::getConnection( $this->getTempConnectionParams() );

		if ( in_array( $dbName, $tmpConn->getSchemaManager()->listDatabases() ) ) {
			$tmpConn->getSchemaManager()->dropDatabase( $dbName );
		}

		$tmpConn->getSchemaManager()->createDatabase( $dbName );
		$tmpConn->close();
	}

	private function recreatePostgresDatabase( $dbName ) {
		$pdo = new \PDO(
			'pgsql:host=' . $GLOBALS['db_host']
			. ';port=' . $GLOBALS['db_port']
			. ';user=' . $GLOBALS['db_username']
			. ';password=' . $GLOBALS['db_password']
		);

		$pdo->exec( 'DROP DATABASE ' . $dbName );
		$pdo->exec( 'CREATE DATABASE ' . $dbName );
	}

	private function getConnectionParams() {
		return array(
			'driver' => $GLOBALS['db_type'],
			'user' => $GLOBALS['db_username'],
			'password' => $GLOBALS['db_password'],
			'host' => $GLOBALS['db_host'],
			'dbname' => $GLOBALS['db_name'],
			'port' => $GLOBALS['db_port']
		);
	}

	private function getTempConnectionParams() {
		$params = array(
			'driver' => $GLOBALS['tmpdb_type'],
			'user' => $GLOBALS['tmpdb_username'],
			'password' => $GLOBALS['tmpdb_password'],
			'host' => $GLOBALS['tmpdb_host'],
			'dbname' => null,
			'port' => $GLOBALS['tmpdb_port']
		);

		if (isset($GLOBALS['tmpdb_name'])) {
			$params['dbname'] = $GLOBALS['tmpdb_name'];
		}

		return $params;
	}

}