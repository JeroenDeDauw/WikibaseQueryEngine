<?php

namespace Wikibase\QueryEngine\Tests\Integration\Console;

use Symfony\Component\Console\Tester\CommandTester;
use Wikibase\QueryEngine\Console\Import\ImportEntitiesCommand;
use Wikibase\QueryEngine\Importer\EntitiesImporter;
use Wikibase\QueryEngine\Tests\Fixtures\FakeEntityIterator;
use Wikibase\QueryEngine\Tests\Integration\IntegrationStoreBuilder;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportEntitiesCommandTest extends \PHPUnit_Framework_TestCase {

	private $command;
	private $store;

	public function setUp() {
		$this->store = IntegrationStoreBuilder::newStore( $this );

		$this->command = new ImportEntitiesCommand();
		$this->command->setDependencies( new EntitiesImporter(
			$this->store->newWriter(),
			new FakeEntityIterator()
		) );

		$this->store->newInstaller()->install();
	}

	public function testEntityIdInOutput() {
		$output = $this->getOutputForArgs( array() );

		$this->assertContains( 'Started import', $output );
		$this->assertContains( 'Importing Q1337... done', $output );
		$this->assertContains( 'Importing Q1347... done', $output );
		$this->assertContains( 'Completed import', $output );
	}

	private function getOutputForArgs( array $args ) {
		$commandTester = $this->newCommandTester();

		$commandTester->execute( $args );

		return $commandTester->getDisplay();
	}

	private function newCommandTester() {
		return new CommandTester( $this->command );
	}

}
