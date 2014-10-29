<?php

namespace Wikibase\QueryEngine\Importer;

use Exception;
use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @since 0.4
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface ImportReporter {

	/**
	 * Gets called when the import started.
	 */
	public function onImportStarted();

	/**
	 * Gets called when the importer starts importing an entity.
	 *
	 * @param EntityDocument $entity
	 */
	public function onEntityInsertStarted( EntityDocument $entity );

	/**
	 * Gets called when the importer successfully imported an entity.
	 *
	 * @param EntityDocument $entity
	 */
	public function onEntityInsertSucceeded( EntityDocument $entity );

	/**
	 * Gets called when the importer failed to import an entity.
	 *
	 * @param EntityDocument $entity
	 * @param Exception $ex
	 */
	public function onEntityInsertFailed( EntityDocument $entity, Exception $ex );

	/**
	 * Gets called when the import successfully completed by running out of things to import.
	 */
	public function onImportCompleted();

	/**
	 * Gets called when the import stopped after receiving the abort command.
	 */
	public function onImportAborted();

}
