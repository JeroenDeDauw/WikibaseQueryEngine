<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\Schema\TableDeletionFailedException;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreUninstaller;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Uninstaller implements QueryStoreUninstaller {

	/**
	 * @var StoreConfig
	 */
	private $config;

	/**
	 * @var TableBuilder
	 */
	private $tableBuilder;

	/**
	 * @var Schema
	 */
	private $storeSchema;

	/**
	 * @param StoreConfig $storeConfig
	 * @param Schema $storeSchema
	 * @param TableBuilder $tableBuilder
	 */
	public function __construct( StoreConfig $storeConfig, Schema $storeSchema, TableBuilder $tableBuilder ) {
		$this->config = $storeConfig;
		$this->storeSchema = $storeSchema;
		$this->tableBuilder = $tableBuilder;
	}

	/**
	 * @param string $message
	 */
	private function report( $message ) {

	}

	/**
	 * @see QueryStoreUninstaller::uninstall
	 *
	 * @throws QueryEngineException
	 */
	public function uninstall() {
		$this->report( 'Starting uninstall of ' . $this->config->getStoreName() );

		try {
			$this->dropTables();
		}
		catch ( TableDeletionFailedException $ex ) {
			throw new QueryEngineException(
				'SQLStore uninstallation failed: ' . $ex->getMessage(),
				0,
				$ex
			);
		}

		$this->report( 'Finished uninstall of ' . $this->config->getStoreName() );
	}

	/**
	 * Removes the tables belonging to the store.
	 * @throws TableDeletionFailedException
	 */
	private function dropTables() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->tableBuilder->dropTable( $table->getName() );
		}
	}

}
