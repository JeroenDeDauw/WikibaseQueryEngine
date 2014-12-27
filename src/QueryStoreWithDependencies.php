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
	 * @since 0.1
	 *
	 * @return DescriptionMatchFinder
	 */
	public function newDescriptionMatchFinder();

	/**
	 * Returns the writer for this store.
	 * The updater allows for updating the data in the store.
	 *
	 * @since 0.1
	 *
	 * @return QueryStoreWriter
	 */
	public function newWriter();

	/**
	 * @since 0.1
	 *
	 * @return QueryStoreInstaller
	 */
	public function newInstaller();

	/**
	 * @since 0.1
	 *
	 * @return QueryStoreUninstaller
	 */
	public function newUninstaller();

	/**
	 * @since 0.1
	 *
	 * @return QueryStoreUpdater
	 */
	public function newUpdater();

}
