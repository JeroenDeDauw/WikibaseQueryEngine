<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\Database\Schema\TableSchemaUpdater;
use Wikibase\QueryEngine\QueryStoreUpdater;
use Wikibase\QueryEngine\SQLStore\Schema;

/**
 * TODO: create integration test
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Updater implements QueryStoreUpdater {

	protected $storeSchema;
	protected $schemaUpdater;
	protected $tableReader;
	protected $tableBuilder;

	public function __construct( Schema $storeSchema, TableSchemaUpdater $schemaUpdater,
		TableDefinitionReader $tableReader, TableBuilder $tableBuilder ) {

		$this->storeSchema = $storeSchema;
		$this->schemaUpdater = $schemaUpdater;
		$this->tableReader = $tableReader;
		$this->tableBuilder = $tableBuilder;
	}

	/**
	 * @see QueryStoreUpdater::update
	 *
	 * TODO: document throws
	 */
	public function update() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			if ( $this->tableBuilder->tableExists( $table->getName() ) ) {
				$this->schemaUpdater->updateTable(
					$this->tableReader->readDefinition( $table->getName() ),
					$table
				);
			}
			else {
				$this->tableBuilder->createTable( $table );
			}
		}
	}

}
