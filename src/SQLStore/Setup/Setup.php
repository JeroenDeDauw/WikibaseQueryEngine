<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\QueryEngine\QueryStoreSetup;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Setup implements QueryStoreSetup {

	/**
	 * @param Installer $installer
	 * @param Updater $updater
	 * @param Uninstaller $uninstaller
	 */
	public function __construct( Installer $installer, Updater $updater, Uninstaller $uninstaller ) {
		$this->installer = $installer;
		$this->updater = $updater;
		$this->uninstaller = $uninstaller;
	}

	/**
	 * @see QueryStoreSetup::install
	 *
	 * @since 0.1
	 *
	 * TODO: document throws
	 */
	public function install() {
		$this->installer->install();
	}

	/**
	 * @see QueryStoreSetup::update
	 *
	 * @since 0.1
	 *
	 * TODO: document throws
	 */
	public function update() {
		$this->updater->update();
	}

	/**
	 * @see QueryStoreSetup::uninstall
	 *
	 * @since 0.1
	 *
	 * TODO: document throws
	 */
	public function uninstall() {
		$this->uninstaller->uninstall();
	}

}
