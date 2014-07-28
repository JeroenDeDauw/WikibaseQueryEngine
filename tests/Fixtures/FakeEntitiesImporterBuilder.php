<?php

namespace Wikibase\QueryEngine\Tests\Fixtures;

use Wikibase\QueryEngine\Importer\EntitiesImporter;
use Wikibase\QueryEngine\Importer\EntitiesImporterBuilder;
use Wikibase\QueryEngine\Importer\ImportReporter;
use Wikibase\QueryEngine\QueryStoreWriter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FakeEntitiesImporterBuilder implements EntitiesImporterBuilder {

	private $writer;
	private $entityIterator;

	private $reporter;

	public function __construct( QueryStoreWriter $writer, \Iterator $entityIterator ) {
		$this->writer = $writer;
		$this->entityIterator = $entityIterator;
	}

	/**
	 * @param int $maxBatchSize
	 */
	public function setBatchSize( $maxBatchSize ) {
	}

	/**
	 * @param $reporter ImportReporter
	 */
	public function setReporter( ImportReporter $reporter ) {
		$this->reporter = $reporter;
	}

	/**
	 * @param string $previousEntityId
	 */
	public function setContinuationId( $previousEntityId ) {
	}

	/**
	 * @return EntitiesImporter
	 */
	public function newImporter() {
		return new EntitiesImporter(
			$this->writer,
			$this->entityIterator,
			$this->reporter
		);
	}

}
