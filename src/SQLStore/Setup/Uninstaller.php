<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Psr\Log\LoggerInterface;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreUninstaller;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Uninstaller implements QueryStoreUninstaller {

	private $logger;
	private $schemaManager;
	private $storeSchema;

	public function __construct( LoggerInterface $logger, StoreSchema $storeSchema, AbstractSchemaManager $schemaManager ) {
		$this->logger = $logger;
		$this->storeSchema = $storeSchema;
		$this->schemaManager = $schemaManager;
	}

	/**
	 * @see QueryStoreUninstaller::uninstall
	 *
	 * @throws QueryEngineException
	 */
	public function uninstall() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			$this->dropTable( $table );
		}
	}

	/**
	 * Removes the tables belonging to the store.
	 *
	 * @param Table $table
	 */
	private function dropTable( Table $table ) {
		try {
			$this->schemaManager->dropTable( $table->getName() );
		}
		catch ( DBALException $ex ) {
			$this->logger->alert( $ex->getMessage(), array( 'exception' => $ex ) );
		}
	}

}
