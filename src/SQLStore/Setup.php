<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\MessageReporter;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\QueryInterface;
use Wikibase\Database\QueryInterfaceException;
use Wikibase\Database\TableBuilder;
use Wikibase\Database\TableDefinition;

/**
 * Setup for the SQLStore.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Setup {

	/**
	 * @since 0.1
	 *
	 * @var StoreConfig
	 */
	private $config;

	/**
	 * @since 0.1
	 *
	 * @var QueryInterface
	 */
	private $queryInterface;

	/**
	 * @since 0.1
	 *
	 * @var TableBuilder
	 */
	private $tableBuilder;

	/**
	 * @since 0.1
	 *
	 * @var MessageReporter|null
	 */
	private $messageReporter;

	/**
	 * @since 0.1
	 *
	 * @var Schema
	 */
	private $storeSchema;

	/**
	 * @since 0.1
	 *
	 * @param StoreConfig $storeConfig
	 * @param Schema $storeSchema
	 * @param QueryInterface $queryInterface
	 * @param TableBuilder $tableBuilder
	 * @param MessageReporter|null $messageReporter
	 */
	public function __construct( StoreConfig $storeConfig, Schema $storeSchema, QueryInterface $queryInterface,
								 TableBuilder $tableBuilder, MessageReporter $messageReporter = null ) {
		$this->config = $storeConfig;
		$this->storeSchema = $storeSchema;
		$this->tableBuilder = $tableBuilder;
		$this->queryInterface = $queryInterface;
		$this->messageReporter = $messageReporter;
	}

	/**
	 * @since 0.1
	 *
	 * @param string $message
	 */
	private function report( $message ) {
		if ( $this->messageReporter !== null ) {
			$this->messageReporter->reportMessage( $message );
		}
	}

	/**
	 * Install the store.
	 *
	 * @since 0.1
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
	 *
	 * @since 0.1
	 */
	private function setupTables() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->tableBuilder->createTable( $table );
		}
	}

	/**
	 * Uninstall the store.
	 *
	 * @since 0.1
	 *
	 * @return boolean Success indicator
	 */
	public function uninstall() {
		$this->report( 'Starting uninstall of ' . $this->config->getStoreName() );

		$success = $this->dropTables();

		$this->report( 'Finished uninstall of ' . $this->config->getStoreName() );

		return $success;
	}

	/**
	 * Removes the tables belonging to the store.
	 *
	 * @since 0.1
	 *
	 * @return boolean Success indicator
	 */
	private function dropTables() {
		$success = true;

		foreach ( $this->storeSchema->getTables() as $table ) {
			$success = $this->queryInterface->dropTable( $table->getName() ) && $success;
		}

		return $success;
	}

}
