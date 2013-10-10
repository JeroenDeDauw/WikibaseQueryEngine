<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Wikibase\Database\QueryInterface\QueryInterfaceException;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\QueryEngine\SQLStore\Schema;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Installer {

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
	 * @see QueryStoreSetup::install
	 *
	 * TODO: document throws
	 */
	public function install() {
		$this->report( 'Starting install of ' . $this->config->getStoreName() );

		try {
			$this->setupTables();
		}
		catch ( QueryInterfaceException $exception ) {
			// TODO: throw exception of proper type
		}

		// TODO: initialize basic content

		$this->report( 'Finished install of ' . $this->config->getStoreName() );
	}

	/**
	 * Sets up the tables of the store.
	 */
	private function setupTables() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->tableBuilder->createTable( $table );
		}
	}

}
