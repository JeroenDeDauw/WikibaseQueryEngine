<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Psr\Log\LoggerInterface;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreInstaller;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Installer implements QueryStoreInstaller {

	private $logger;
	private $schemaManager;
	private $storeSchema;

	public function __construct( LoggerInterface $logger, StoreSchema $storeSchema, AbstractSchemaManager $schemaManager ) {
		$this->logger = $logger;
		$this->storeSchema = $storeSchema;
		$this->schemaManager = $schemaManager;
	}

	/**
	 * @see QueryStoreInstaller::install
	 *
	 * @throws QueryEngineException
	 */
	public function install() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->setupTable( $table );
		}
	}

	/**
	 * Sets up the tables of the store.
	 *
	 * @param Table $table
	 */
	private function setupTable( Table $table ) {
		try {
			$this->schemaManager->createTable( $table );
		}
		catch ( DBALException $ex ) {
			$this->logger->alert( $ex->getMessage(), array( 'exception' => $ex ) );
		}
	}

}
