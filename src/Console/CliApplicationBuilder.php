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
 * Applications that want to provide their own CLI can either use the
 * application constructed here and override the configuration, or
 * simply construct an Application object on their own and use the
 * Commands defined by this library.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CliApplicationBuilder {

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
		$this->app->setName( 'Wikibase Query CLI' );
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