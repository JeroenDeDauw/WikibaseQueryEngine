<?php

namespace Wikibase\QueryEngine\Console;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpSqlCommand extends Command {

	/**
	 * @var StoreSchema
	 */
	private $storeSchema;

	/**
	 * @var AbstractPlatform
	 */
	private $platform;

	public function setDependencies( StoreSchema $storeSchema, AbstractPlatform $platform ) {
		$this->storeSchema = $storeSchema;
		$this->platform = $platform;
	}

	protected function configure() {
		$this->setName( 'dump-sql' );
		$this->setDescription( 'Dumps the SQL for creating the QueryEngine SQL store schema' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		foreach ( $this->getQueries() as $query ) {
			$output->writeln( $query );
		}
	}

	private function getQueries() {
		$schema = new Schema( $this->storeSchema->getTables() );
		return $schema->toSql( $this->platform );
	}

}
