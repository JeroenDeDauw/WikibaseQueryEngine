<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreUninstaller;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Uninstaller implements QueryStoreUninstaller {

	private $config;
	private $schemaManager;
	private $storeSchema;

	public function __construct( StoreConfig $storeConfig, StoreSchema $storeSchema, AbstractSchemaManager $schemaManager ) {
		$this->config = $storeConfig;
		$this->storeSchema = $storeSchema;
		$this->schemaManager = $schemaManager;
	}

	/**
	 * @see QueryStoreUninstaller::uninstall
	 *
	 * @throws QueryEngineException
	 */
	public function uninstall() {
		try {
			$this->dropTables();
		}
		catch ( DBALException $ex ) {
			throw new QueryEngineException(
				'SQLStore uninstallation failed: ' . $ex->getMessage(),
				0,
				$ex
			);
		}
	}

	/**
	 * Removes the tables belonging to the store.
	 * @throws DBALException
	 */
	private function dropTables() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->schemaManager->dropTable( $table->getName() );
		}
	}

}
