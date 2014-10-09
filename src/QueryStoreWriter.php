<?php

namespace Wikibase\QueryEngine;

use Wikibase\DataModel\Entity\EntityDocument;

/**
 * Updater for a query store.
 * Implementing objects provide an interface via which new data can be inserted
 * into the query store, existing data can be updated and existing data can be removed.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStoreWriter {

	/**
	 * @see QueryStoreUpdater::insertEntity
	 *
	 * @since 0.1
	 *
	 * @param EntityDocument $entity
	 */
	public function insertEntity( EntityDocument $entity );

	/**
	 * @see QueryStoreUpdater::updateEntity
	 *
	 * @since 0.1
	 *
	 * @param EntityDocument $entity
	 */
	public function updateEntity( EntityDocument $entity );

	/**
	 * @see QueryStoreUpdater::deleteEntity
	 *
	 * @since 0.1
	 *
	 * @param EntityDocument $entity
	 */
	public function deleteEntity( EntityDocument $entity );

}
