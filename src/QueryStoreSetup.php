<?php

namespace Wikibase\QueryEngine;

use Wikibase\Database\MessageReporter;

/**
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseQueryStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface QueryStoreSetup {

	/**
	 * Install the store.
	 * This includes setting up all the required database tables.
	 *
	 * @since 0.1
	 *
	 * TODO: document throws
	 */
	public function install();

	/**
	 * Uninstall the store.
	 * This includes removing all the required database tables.
	 *
	 * Caution: all data held by the store will be removed
	 *
	 * @since 0.1
	 *
	 * TODO: document throws
	 */
	public function uninstall();

	/**
	 * Sets the MessageReporter to report messages with.
	 *
	 * @since 0.1
	 *
	 * @param MessageReporter $reporter
	 */
	public function setMessageReporter( MessageReporter $reporter );

}
