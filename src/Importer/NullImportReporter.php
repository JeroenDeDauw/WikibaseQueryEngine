<?php

namespace Wikibase\QueryEngine\Importer;

use Wikibase\DataModel\Entity\Entity;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class NullImportReporter implements ImportReporter {

	public function onImportStarted() {}
	public function onEntityInsertStarted( Entity $entity ) {}
	public function onEntityInsertSucceeded( Entity $entity ) {}
	public function onEntityInsertFailed( Entity $entity, \Exception $ex ) {}
	public function onImportCompleted() {}
	public function onImportAborted() {}

}
