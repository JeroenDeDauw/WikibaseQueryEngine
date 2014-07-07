<?php

namespace Wikibase\QueryEngine\Importer;

use Iterator;
use RuntimeException;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\QueryEngine\QueryStoreWriter;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntitiesImporter {

	private $storeWriter;
	private $entityIterator;

	/**
	 * @var ImportReporter|null
	 */
	private $reporter = null;

	private $shouldStop = false;

	/**
	 * @param QueryStoreWriter $storeWriter
	 * @param Iterator $entityIterator Each value should be of type Entity
	 */
	public function __construct( QueryStoreWriter $storeWriter, Iterator $entityIterator  ) {
		$this->storeWriter = $storeWriter;
		$this->entityIterator = $entityIterator;
	}

	public function setReporter( ImportReporter $reporter ) {
		$this->reporter = $reporter;
	}

	public function run() {
		$this->verifyReporterIsSet();

		$this->reporter->onImportStarted();

		foreach ( $this->entityIterator as $entity ) {
			pcntl_signal_dispatch();

			if ( $this->shouldStop ) {
				$this->shouldStop = false;
				$this->reporter->onImportAborted();
				return;
			}

			$this->importEntity( $entity );
		}

		$this->reporter->onImportCompleted();
	}

	private function importEntity( Entity $entity ) {
		$this->reporter->onEntityInsertStarted( $entity );
		$wasSuccessful = false;

		try {
			$this->storeWriter->updateEntity( $entity );
			$wasSuccessful = true;
		}
		catch ( \Exception $ex ) {
			$this->reporter->onEntityInsertFailed( $entity, $ex );
		}

		if ( $wasSuccessful ) {
			$this->reporter->onEntityInsertSucceeded( $entity );
		}
	}

	public function stop() {
		$this->shouldStop = true;
	}

	private function verifyReporterIsSet() {
		if ( $this->reporter === null ) {
			throw new RuntimeException(
				'A reporter needs to be set via setReporter before running the EntitiesInserter'
			);
		}
	}

}
