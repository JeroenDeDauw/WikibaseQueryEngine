<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\SchemaModifier;
use Wikibase\Database\Schema\SimpleTableSchemaUpdater;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\Schema\TableDefinitionReader;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\QueryEngine\QueryEngine;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder;
use Wikibase\QueryEngine\SQLStore\Engine\Engine;
use Wikibase\QueryEngine\SQLStore\Setup\Installer;
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
 * This class is the top level factory able to construct
 * the high level services that form the public interface
 * of the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SQLStore {

	/**
	 * @var StoreConfig
	 */
	private $config;

	/**
	 * @var Schema|null
	 */
	protected $schema = null;

	public function __construct( StoreConfig $config ) {
		$this->config = $config;
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

	/**
	 * @since 0.1
	 *
	 * @param QueryInterface $queryInterface
	 *
	 * @return QueryEngine
	 */
	public function newQueryEngine( QueryInterface $queryInterface ) {
		return new Engine(
			$this->newDescriptionMatchFinder( $queryInterface )
		);
	}

	/**
	 * @since 0.1
	 *
	 * @param QueryInterface $queryInterface
	 *
	 * @return QueryStoreWriter
	 */
	public function newWriter( QueryInterface $queryInterface ) {
		return new Writer(
			$this->newEntityInserter( $queryInterface ),
			$this->newEntityUpdater( $queryInterface ),
			$this->newEntityRemover( $queryInterface )
		);
	}

	public function newInstaller( TableBuilder $tableBuilder ) {
		return new Installer(
			$this->config,
			$this->getSchema(),
			$tableBuilder
		);
	}

	public function newUninstaller( TableBuilder $tableBuilder ) {
		return new Uninstaller(
			$this->config,
			$this->getSchema(),
			$tableBuilder
		);
	}

	public function newUpdater( TableBuilder $tableBuilder, TableDefinitionReader $tableDefinitionReader, SchemaModifier $schemaModifier ) {
		return new Updater(
			$this->getSchema(),
			new SimpleTableSchemaUpdater( $schemaModifier ),
			$tableDefinitionReader,
			$tableBuilder
		);
	}

	private function newEntityInserter( QueryInterface $queryInterface ) {
		return new EntityInserter(
			$this->newClaimInserter( $queryInterface )
		);
	}

	private function newEntityUpdater( QueryInterface $queryInterface ) {
		return new EntityUpdater(
			$this->newEntityRemover( $queryInterface ),
			$this->newEntityInserter( $queryInterface )
		);
	}

	private function newEntityRemover( QueryInterface $queryInterface ) {
		return new EntityRemover(
			$this->newSnakRemover( $queryInterface )
		);
	}

	private function newSnakRemover( QueryInterface $queryInterface ) {
		return new SnakRemover( $this->getSnakStores( $queryInterface ) );
	}

	private function newEntityTable( QueryInterface $queryInterface ) {
		return new EntityTable(
			$queryInterface,
			$this->getSchema()->getEntitiesTable()->getName()
		);
	}

	private function newClaimInserter( QueryInterface $queryInterface ) {
		return new ClaimInserter(
			$this->newSnakInserter( $queryInterface ),
			new ClaimRowBuilder()
		);
	}

	private function newSnakInserter( QueryInterface $queryInterface ) {
		return new SnakInserter(
			$this->getSnakStores( $queryInterface ),
			new SnakRowBuilder()
		);
	}

	/**
	 * @param QueryInterface $queryInterface
	 * @return SnakStore[]
	 */
	private function getSnakStores( QueryInterface $queryInterface ) {
		return array(
			new ValueSnakStore(
				$queryInterface,
				$this->getSchema()->getDataValueHandlers( SnakRole::MAIN_SNAK ),
				SnakRole::MAIN_SNAK
			),
			new ValueSnakStore(
				$queryInterface,
				$this->getSchema()->getDataValueHandlers( SnakRole::QUALIFIER ),
				SnakRole::QUALIFIER
			),
			new ValuelessSnakStore(
				$queryInterface,
				$this->getSchema()->getValuelessSnaksTable()->getName()
			)
		);
	}

	private function newDescriptionMatchFinder( QueryInterface $queryInterface ) {
		return new DescriptionMatchFinder(
			$queryInterface,
			$this->getSchema(),
			$this->config->getPropertyDataValueTypeLookup(),
			new BasicEntityIdParser() // TODO: inject
		);
	}

}
