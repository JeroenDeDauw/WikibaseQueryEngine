<?php

namespace Wikibase\QueryEngine;

/**
 * Interface for query stores providing access to all needed sub components
 * such as updaters, query engines and setup/teardown operations.
 *
 * This interface somewhat acts as facade to the query component.
 * All access to a specific store should typically happen via this interface.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseQueryStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStore {

	// TODO: create store factory and figure out how to inject dependencies
	// for the typical Wikibase repo use case.

	/**
	 * Returns the name of the query store. This name can be configuration dependent
	 * and is thus not always the same for a certain store type. For instance, you can
	 * have "Wikibase SQL store" and "Wikibase SQL store for update to new config".
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns the query engine for this store.
	 * The query engine allows running queries against the store.
	 *
	 * @since 0.1
	 *
	 * @return QueryEngine
	 */
	public function getQueryEngine();

	/**
	 * Returns the updater for this store.
	 * The updater allows for updating the data in the store.
	 *
	 * @since 0.1
	 *
	 * @return QueryStoreWriter
	 */
	public function getUpdater();

	/**
	 * Sets up the store.
	 * This means creating and initializing the storage structures
	 * required for storing data in the store.
	 *
	 * @since 0.1
	 *
	 * @return QueryStoreSetup
	 */
	public function newSetup();

}
