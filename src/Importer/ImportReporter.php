<?php

namespace Wikibase\QueryEngine\Importer;

use Wikibase\DataModel\Entity\Entity;

/**
 * @since 0.3
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
	 */
	public function onEntityInsertStarted( Entity $entity );

	/**
	 * Gets called when the importer successfully imported an entity.
	 */
	public function onEntityInsertSucceeded( Entity $entity );

	/**
	 * Gets called when the importer failed to import an entity.
	 */
	public function onEntityInsertFailed( Entity $entity, \Exception $ex );

	/**
	 * Gets called when the import successfully completed by running out of things to import.
	 */
	public function onImportCompleted();

	/**
	 * Gets called when the import stopped after receiving the abort command.
	 */
	public function onImportAborted();

}
