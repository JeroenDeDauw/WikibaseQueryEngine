<?php

namespace Wikibase\QueryEngine;

/**
 * Updates the store to the latest version of its schema.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStoreUpdater {

	/**
	 * @throws QueryEngineException
	 */
	public function update();

}
