<?php

namespace Wikibase\QueryEngine;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStoreInstaller {

	/**
	 * Does the tasks needed before the store can be used,
	 * such as for instance creating the required database tables.
	 *
	 * @throws QueryEngineException
	 */
	public function install();

}
