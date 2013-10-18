<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\SchemaModifier;
use Wikibase\Database\Schema\SimpleTableSchemaUpdater;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\Database\Schema\TableSchemaUpdater;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\QueryEngine\QueryEngine;
use Wikibase\QueryEngine\QueryStore;
use Wikibase\QueryEngine\QueryStoreSetup;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder;
use Wikibase\QueryEngine\SQLStore\Engine\Engine;
use Wikibase\QueryEngine\SQLStore\Setup\Installer;
use Wikibase\QueryEngine\SQLStore\Setup\Setup;
use Wikibase\QueryEngine\SQLStore\Setup\Uninstaller;
use Wikibase\QueryEngine\SQLStore\Setup\Updater;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRowBuilder;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;
use Wikibase\SnakRole;

/**
 * Simple query store for relational SQL databases.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Store implements QueryStore {

	/**
	 * @var StoreConfig
	 */
	private $config;

	/**
	 * @var QueryInterface
	 */
	private $queryInterface;

	/**
	 * @var TableBuilder
	 */
	private $tableBuilder;

	/**
	 * @var TableDefinitionReader
	 */
	public $tableDefinitionReader;

	/**
	 * @var SchemaModifier
	 */
	public $schemaModifier;

	/**
	 * @var Schema|null
	 */
	protected $schema = null;

	public function __construct( StoreConfig $config, QueryInterface $queryInterface,
		TableBuilder $tableBuilder, TableDefinitionReader $tableDefinitionReader, SchemaModifier $schemaModifier ) {

		$this->config = $config;

		$this->queryInterface = $queryInterface;
		$this->tableBuilder = $tableBuilder;
		$this->tableDefinitionReader = $tableDefinitionReader;
		$this->schemaModifier = $schemaModifier;
	}

	/**
	 * @see QueryStore::getName
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getName() {
		return $this->config->getStoreName();
	}

	/**
	 * @see QueryStore::getQueryEngine
	 *
	 * @since 0.1
	 *
	 * @return QueryEngine
	 */
	public function getQueryEngine() {
		return new Engine(
			$this->newDescriptionMatchFinder()
		);
	}

	/**
	 * @see QueryStore::getWriter
	 *
	 * @since 0.1
	 *
	 * @return QueryStoreWriter
	 */
	public function getWriter() {
		return $this->newWriter();
	}

	/**
	 * @see QueryStore::newSetup
	 *
	 * @since 0.1
	 *
	 * @return QueryStoreSetup
	 */
	public function newSetup() {
		return new Setup(
			$this->getInstaller(),
			$this->getUpdater(),
			$this->getUninstaller()
		);
	}

	/**
	 * @return Schema
	 */
	private function getSchema() {
		if ( $this->schema === null ) {
			$this->schema = new Schema( $this->config );
		}

		return $this->schema;
	}

	private function newEntityInserter() {
		return new EntityInserter(
			$this->newClaimInserter()
		);
	}

	private function newEntityUpdater() {
		return new EntityUpdater(
			$this->newEntityRemover(),
			$this->newEntityInserter()
		);
	}

	private function newEntityRemover() {
		return new EntityRemover(
			$this->newSnakRemover()
		);
	}

	private function newSnakRemover() {
		return new SnakRemover( $this->getSnakStores() );
	}

	private function newEntityTable() {
		return new EntityTable(
			$this->queryInterface,
			$this->getSchema()->getEntitiesTable()->getName()
		);
	}

	private function newClaimInserter() {
		return new ClaimInserter(
			$this->newSnakInserter(),
			new ClaimRowBuilder()
		);
	}

	private function newSnakInserter() {
		return new SnakInserter(
			$this->getSnakStores(),
			new SnakRowBuilder()
		);
	}

	/**
	 * @return SnakStore[]
	 */
	private function getSnakStores() {
		return array(
			new ValueSnakStore(
				$this->queryInterface,
				$this->getSchema()->getDataValueHandlers( SnakRole::MAIN_SNAK ),
				SnakRole::MAIN_SNAK
			),
			new ValueSnakStore(
				$this->queryInterface,
				$this->getSchema()->getDataValueHandlers( SnakRole::QUALIFIER ),
				SnakRole::QUALIFIER
			),
			new ValuelessSnakStore(
				$this->queryInterface,
				$this->getSchema()->getValuelessSnaksTable()->getName()
			)
		);
	}

	private function newWriter() {
		return new Writer(
			$this->newEntityInserter(),
			$this->newEntityUpdater(),
			$this->newEntityRemover()
		);
	}

	/**
	 * @return DescriptionMatchFinder
	 */
	private function newDescriptionMatchFinder() {
		return new DescriptionMatchFinder(
			$this->queryInterface,
			$this->getSchema(),
			$this->config->getPropertyDataValueTypeLookup(),
			new BasicEntityIdParser() // TODO: inject
		);
	}

	private function getInstaller() {
		return new Installer(
			$this->config,
			$this->getSchema(),
			$this->tableBuilder
		);
	}

	private function getUninstaller() {
		return new Uninstaller(
			$this->config,
			$this->getSchema(),
			$this->tableBuilder
		);
	}

	private function getUpdater() {
		return new Updater(
			$this->getSchema(),
			$this->getTableSchemaUpdater(),
			$this->tableDefinitionReader,
			$this->tableBuilder
		);
	}

	/**
	 * @return TableSchemaUpdater
	 */
	private function getTableSchemaUpdater() {
		return new SimpleTableSchemaUpdater(
			$this->schemaModifier
		);
	}

}
