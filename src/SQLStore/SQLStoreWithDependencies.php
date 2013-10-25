<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\SchemaModifier;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\QueryEngine\QueryStoreWithDependencies;

class SQLStoreWithDependencies implements QueryStoreWithDependencies {

	protected $queryInterface;
	protected $tableBuilder;
	protected $tableReader;
	protected $schemaModifier;

	protected $store;

	public function __construct( SQLStore $factory, QueryInterface $queryInterface,
		TableBuilder $tableBuilder, TableDefinitionReader $tableReader, SchemaModifier $schemaModifier ) {

		$this->factory = $factory;
		$this->queryInterface = $queryInterface;
		$this->tableBuilder = $tableBuilder;
		$this->tableReader = $tableReader;
		$this->schemaModifier = $schemaModifier;
	}

	public function newQueryEngine() {
		return $this->factory->newQueryEngine( $this->queryInterface );
	}

	public function newWriter() {
		return $this->factory->newWriter( $this->queryInterface );
	}

	public function newInstaller() {
		return $this->factory->newInstaller( $this->tableBuilder );
	}

	public function newUninstaller() {
		return $this->factory->newUninstaller( $this->tableBuilder );
	}

	public function newUpdater() {
		return $this->factory->newUpdater(
			$this->tableBuilder,
			$this->tableReader,
			$this->schemaModifier
		);
	}

}