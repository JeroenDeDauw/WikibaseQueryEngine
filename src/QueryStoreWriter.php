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
	 * Inserts an entity into the store.
	 * Only use this when it is known the entity is not already in the store.
	 * No checks will be made if it is already, so if it is, data might get
	 * inserted twice and unique key constrain violation errors might occur.
	 * In such cases the @see updateEntity method should be used.
	 *
	 * @since 0.1
	 *
	 * @param EntityDocument $entity
	 */
	public function insertEntity( EntityDocument $entity );

	/**
	 * Updates the store to reflect the state of the provided entity.
	 * If the entity already existed in the store, the old values
	 * will be updated or deleted as appropriate.
	 *
	 * @since 0.1
	 *
	 * @param EntityDocument $entity
	 */
	public function updateEntity( EntityDocument $entity );

	/**
	 * Removes an entity from the store if it is present.
	 *
	 * @since 0.1
	 *
	 * @param EntityDocument $entity
	 */
	public function deleteEntity( EntityDocument $entity );

}
