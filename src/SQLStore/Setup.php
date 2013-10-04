<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\MessageReporter;
use Wikibase\Database\QueryInterface\QueryInterfaceException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\QueryEngine\QueryStoreSetup;

/**
 * Setup for the SQLStore.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Setup implements QueryStoreSetup {

	/**
	 * @since 0.1
	 *
	 * @var StoreConfig
	 */
	private $config;

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
	 * @param TableBuilder $tableBuilder
	 * @param MessageReporter|null $messageReporter
	 */
	public function __construct( StoreConfig $storeConfig, Schema $storeSchema,
								 TableBuilder $tableBuilder, MessageReporter $messageReporter = null ) {
		$this->config = $storeConfig;
		$this->storeSchema = $storeSchema;
		$this->tableBuilder = $tableBuilder;
		$this->messageReporter = $messageReporter;
	}

	/**
	 * @see QueryStoreSetup::setMessageReporter
	 *
	 * @since 0.1
	 *
	 * @param MessageReporter $reporter
	 */
	public function setMessageReporter( MessageReporter $reporter ) {
		$this->messageReporter = $reporter;
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
	 * @see QueryStoreSetup::install
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
	 * @see QueryStoreSetup::uninstall
	 *
	 * @since 0.1
	 */
	public function uninstall() {
		$this->report( 'Starting uninstall of ' . $this->config->getStoreName() );

		$this->dropTables();

		$this->report( 'Finished uninstall of ' . $this->config->getStoreName() );

		// TODO: thow exception on failure
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
			$this->tableBuilder->dropTable( $table->getName() );
		}

		return $success; // TODO: remove, or switch to using a try catch
	}

}
