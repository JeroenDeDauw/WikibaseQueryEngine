<?php

namespace Wikibase\QueryEngine\Console;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Symfony\Component\Console\Application;
use Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * Builds the QueryEngine CLI application.
 * It adds the QueryEngine CLI commands with some demo configuration.
 *
 * This class is package private. Applications that use QueryEngine
 * and that want to include QueryEngine commands in their own CLI
 * should use their own Application object.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CliApplicationFactory {

	/**
	 * @var Application
	 */
	private $app;

	/**
	 * @return Application
	 */
	public function newApplication() {
		$this->app = new Application();

		$this->setApplicationInfo();
		$this->registerCommands();

		return $this->app;
	}

	private function setApplicationInfo() {
		$this->app->setName( 'Wikibase QueryEngine CLI' );
		$this->app->setVersion( WIKIBASE_QUERYENGINE_VERSION );
	}

	private function registerCommands() {
		$this->app->add( $this->newDumpCommand() );
	}

	private function newDumpCommand() {
		$command = new DumpSqlCommand();
		$command->setDependencies( $this->getSchema(), new MySqlPlatform() );
		return $command;
	}

	private function getSchema() {
		$handlerBuilder = new DataValueHandlersBuilder();
		$handlers = $handlerBuilder->withSimpleHandlers()->getHandlers();

		return new StoreSchema( 'qr_', $handlers );
	}

}
