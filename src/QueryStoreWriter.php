<?php

namespace Wikibase\QueryEngine;

use Wikibase\DataModel\Entity\Entity;

/**
 * Updater for a query store.
 * Implementing objects provide an interface via which new data can be inserted
 * into the query store, existing data can be updated and existing data can be removed.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseQueryStore
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
	 * @param Entity $entity
	 */
	public function insertEntity( Entity $entity );

	/**
	 * @see QueryStoreUpdater::updateEntity
	 *
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function updateEntity( Entity $entity );

	/**
	 * @see QueryStoreUpdater::deleteEntity
	 *
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function deleteEntity( Entity $entity );

}
