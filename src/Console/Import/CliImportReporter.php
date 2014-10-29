<?php

namespace Wikibase\QueryEngine\Console\Import;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\Importer\ImportReporter;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CliImportReporter implements ImportReporter {

	private $output;

	public function __construct( OutputInterface $output ) {
		$this->output = $output;
	}

	public function onImportStarted() {
		$this->output->writeln( '<info>Started import</info>' );
	}

	public function onEntityInsertStarted( EntityDocument $entity ) {
		$this->output->write( '<info>Importing ' . $entity->getId()->getSerialization() . '... </info>' );
	}

	public function onEntityInsertSucceeded( EntityDocument $entity ) {
		$this->output->writeln( '<info>done.</info>' );
	}

	public function onEntityInsertFailed( EntityDocument $entity, Exception $ex ) {
		$this->output->writeln( '<info>failed!</info>' );
		$this->output->writeln( '<error>' . $ex->getMessage() . '</error>' );
	}

	public function onImportCompleted() {
		$this->output->writeln( '<info>Completed import</info>' );
	}

	public function onImportAborted() {
		$this->output->writeln( '<info>Aborted import</info>' );
	}

}
