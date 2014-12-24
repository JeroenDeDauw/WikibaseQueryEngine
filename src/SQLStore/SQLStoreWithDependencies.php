<?php

namespace Wikibase\QueryEngine\SQLStore;

use Doctrine\DBAL\Connection;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\QueryStoreWithDependencies;

class SQLStoreWithDependencies implements QueryStoreWithDependencies {

	private $factory;
	private $connection;
	private $lookup;
	private $idParser;

	public function __construct( SQLStore $factory, Connection $connection,
		PropertyDataValueTypeLookup $lookup, EntityIdParser $idParser ) {

		$this->factory = $factory;
		$this->connection = $connection;
		$this->lookup = $lookup;
		$this->idParser = $idParser;
	}

	public function newQueryEngine() {
		return $this->factory->newQueryEngine( $this->connection, $this->lookup, $this->idParser );
	}

	public function newWriter() {
		return $this->factory->newWriter( $this->connection );
	}

	public function newInstaller() {
		return $this->factory->newInstaller( $this->connection->getSchemaManager() );
	}

	public function newUninstaller() {
		return $this->factory->newUninstaller( $this->connection->getSchemaManager() );
	}

	public function newUpdater() {
		return $this->factory->newUpdater( $this->connection->getSchemaManager() );
	}

	public function __destruct() {
		$this->connection->close();
	}

}
