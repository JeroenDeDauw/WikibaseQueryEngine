<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreUpdater;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * TODO: create integration test
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Updater implements QueryStoreUpdater {

	private $storeSchema;
	private $schemaManager;

	public function __construct( StoreSchema $storeSchema, AbstractSchemaManager $schemaManager ) {
		$this->storeSchema = $storeSchema;
		$this->schemaManager = $schemaManager;
	}

	/**
	 * @see QueryStoreUpdater::update
	 *
	 * @throws QueryEngineException
	 */
	public function update() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			try {
				$this->handleTable( $table );
			}
			catch ( DBALException $ex ) {
				throw new QueryEngineException(
					'SQLStore uninstallation failed: ' . $ex->getMessage(),
					0,
					$ex
				);
			}
		}
	}

	private function handleTable( Table $table ) {
		if ( $this->schemaManager->tablesExist( $table->getName() ) ) {
			$this->migrateTable( $table );
		}
		else {
			$this->createTable( $table );
		}
	}

	private function createTable( Table $table ) {
		$this->schemaManager->createTable( $table );
	}

	private function migrateTable( Table $table ) {
		$comparator = new Comparator();

		$tableDiff = $comparator->diffTable(
			$this->schemaManager->listTableDetails( $table->getName() ),
			$table
		);

		if ( $tableDiff !== false ) {
			$this->schemaManager->alterTable( $tableDiff );
		}
	}

}
