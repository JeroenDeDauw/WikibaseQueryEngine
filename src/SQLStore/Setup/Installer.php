<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreInstaller;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Installer implements QueryStoreInstaller {

	private $config;
	private $schemaManager;
	private $storeSchema;

	public function __construct( StoreConfig $storeConfig, StoreSchema $storeSchema, AbstractSchemaManager $schemaManager ) {
		$this->config = $storeConfig;
		$this->storeSchema = $storeSchema;
		$this->schemaManager = $schemaManager;
	}

	/**
	 * @see QueryStoreInstaller::install
	 *
	 * @throws QueryEngineException
	 */
	public function install() {
		try {
			$this->setupTables();
		}
		catch ( DBALException $ex ) {
			throw new QueryEngineException(
				'SQLStore installation failed: ' . $ex->getMessage(),
				0,
				$ex
			);
		}

		// TODO: initialize basic content
	}

	/**
	 * Sets up the tables of the store.
	 *
	 * @throws DBALException
	 */
	private function setupTables() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->schemaManager->createTable( $table );
		}
	}

}
