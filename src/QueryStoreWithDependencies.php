<?php

namespace Wikibase\QueryEngine;

/**
 * Coarse interface that provides access to the high level public interfaces
 * all stores have. Instantiating an implementation likely requires instantiating
 * all its collaborators. It is thus advisable to generally use just construct
 * the needed service(s).
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStoreWithDependencies {

	/**
	 * Returns the query engine for this store.
	 * The query engine allows running queries against the store.
	 *
	 * @return QueryEngine
	 */
	public function newQueryEngine();

	/**
	 * Returns the writer for this store.
	 * The updater allows for updating the data in the store.
	 *
	 * @return QueryStoreWriter
	 */
	public function newWriter();

	/**
	 * @return QueryStoreInstaller
	 */
	public function newInstaller();

	/**
	 * @return QueryStoreUninstaller
	 */
	public function newUninstaller();

	/**
	 * @return QueryStoreUpdater
	 */
	public function newUpdater();

}
