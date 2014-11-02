<?php

namespace Wikibase\QueryEngine;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStoreUninstaller {

	/**
	 * Removes the things set up by the @see QueryStoreInstaller.
	 * This might for instance be removal of database tables.
	 * Caution: this is a destructive action!
	 *
	 * @throws QueryEngineException
	 */
	public function uninstall();

}
