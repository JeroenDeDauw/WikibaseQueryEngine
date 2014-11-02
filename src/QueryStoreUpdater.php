<?php

namespace Wikibase\QueryEngine;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStoreUpdater {

	/**
	 * Updates the things set up by the @see QueryStoreInstaller.
	 * This might for instance be addition of new database tables and migration of existing ones.
	 *
	 * @throws QueryEngineException
	 */
	public function update();

}
