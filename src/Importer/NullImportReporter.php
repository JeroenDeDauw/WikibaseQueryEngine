<?php

namespace Wikibase\QueryEngine\Importer;

use Exception;
use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NullImportReporter implements ImportReporter {

	public function onImportStarted() {}
	public function onEntityInsertStarted( EntityDocument $entity ) {}
	public function onEntityInsertSucceeded( EntityDocument $entity ) {}
	public function onEntityInsertFailed( EntityDocument $entity, Exception $ex ) {}
	public function onImportCompleted() {}
	public function onImportAborted() {}

}
