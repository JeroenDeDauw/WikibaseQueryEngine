<?php

namespace Wikibase\QueryEngine\Console\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\QueryEngine\Importer\EntitiesImporter;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportEntitiesCommand extends Command {

	/**
	 * @var EntitiesImporter
	 */
	private $entitiesImporter;

	public function setDependencies( EntitiesImporter $entitiesImporter ) {
		$this->entitiesImporter = $entitiesImporter;
	}

	protected function configure() {
		$this->setName( 'import' );
		$this->setDescription( 'Imports a collection of entities into the QueryEngine store' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		pcntl_signal( SIGINT, array( $this->entitiesImporter, 'stop' ) );
		pcntl_signal( SIGTERM, array( $this->entitiesImporter, 'stop' ) );

		$this->entitiesImporter->setReporter( new CliImportReporter( $output ) );
		$this->entitiesImporter->run();
	}

}
