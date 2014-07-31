<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\Importer;

use Wikibase\QueryEngine\Importer\EntitiesImporter;
use Wikibase\QueryEngine\Tests\Fixtures\FakeEntityIterator;

/**
 * @covers Wikibase\QueryEngine\Importer\EntitiesImporter
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntitiesImporterTest extends \PHPUnit_Framework_TestCase {

	public function testWhenReporterIsNotSet_importStillRuns() {
		$importer = new EntitiesImporter(
			$this->getMock( 'Wikibase\QueryEngine\QueryStoreWriter' ),
			new \ArrayIterator()
		);

		$importer->run();
	}

	public function testWriterAndReporterAreCalledForEachEntity() {
		$entityIterator = new FakeEntityIterator();
		$aCallForEachEntity = $this->exactly( iterator_count( $entityIterator ) );

		$storeWriter = $this->getMock( 'Wikibase\QueryEngine\QueryStoreWriter' );
		$storeWriter->expects( clone $aCallForEachEntity )
			->method( 'updateEntity' );

		$reporter = $this->getMock( 'Wikibase\QueryEngine\Importer\ImportReporter' );
		$reporter->expects( clone $aCallForEachEntity )
			->method( 'onEntityInsertStarted' );

		$reporter->expects( clone $aCallForEachEntity )
			->method( 'onEntityInsertSucceeded' );
		$reporter->expects( $this->never() )
			->method( 'onEntityInsertFailed' );

		$importer = new EntitiesImporter( $storeWriter, $entityIterator, $reporter );

		$importer->run();
	}

}
