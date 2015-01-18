<?php

namespace Wikibase\QueryEngine\Importer;

use Exception;
use Iterator;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryStoreWriter;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntitiesImporter {

	private $storeWriter;
	private $entityIterator;
	private $reporter;

	private $shouldStop = false;

	/**
	 * @param QueryStoreWriter $storeWriter
	 * @param Iterator $entityIterator Each value should be of type Entity
	 * @param ImportReporter|null $reporter
	 */
	public function __construct( QueryStoreWriter $storeWriter, Iterator $entityIterator, ImportReporter $reporter = null ) {
		$this->storeWriter = $storeWriter;
		$this->entityIterator = $entityIterator;
		$this->reporter = $reporter === null ? new NullImportReporter() : $reporter;
	}

	public function run() {
		$this->reporter->onImportStarted();

		foreach ( $this->entityIterator as $entity ) {
			if ( function_exists( 'pcntl_signal_dispatch' ) ) {
				pcntl_signal_dispatch();
			}

			if ( $this->shouldStop ) {
				$this->shouldStop = false;
				$this->reporter->onImportAborted();
				return;
			}

			$this->importEntity( $entity );
		}

		$this->reporter->onImportCompleted();
	}

	private function importEntity( EntityDocument $entity ) {
		$this->reporter->onEntityInsertStarted( $entity );
		$wasSuccessful = false;

		try {
			$this->storeWriter->updateEntity( $entity );
			$wasSuccessful = true;
		}
		catch ( Exception $ex ) {
			$this->reporter->onEntityInsertFailed( $entity, $ex );
		}

		if ( $wasSuccessful ) {
			$this->reporter->onEntityInsertSucceeded( $entity );
		}
	}

	public function stop() {
		$this->shouldStop = true;
	}

}
