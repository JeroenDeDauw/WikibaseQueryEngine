<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Wikibase\Database\Schema\TableBuilder;
use Wikibase\QueryEngine\QueryStoreUninstaller;
use Wikibase\QueryEngine\QueryStoreUpdater;
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
	 * TODO: document throws
	 */
	public function uninstall() {
		$this->report( 'Starting uninstall of ' . $this->config->getStoreName() );

		$this->dropTables();

		$this->report( 'Finished uninstall of ' . $this->config->getStoreName() );

		// TODO: throw exception on failure
	}

	/**
	 * Removes the tables belonging to the store.
	 *
	 * @return boolean Success indicator
	 */
	private function dropTables() {
		$success = true;

		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->tableBuilder->dropTable( $table->getName() );
		}

		return $success; // TODO: remove, or switch to using a try catch
	}

}
