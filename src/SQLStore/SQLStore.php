<?php

namespace Wikibase\QueryEngine\SQLStore;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\QueryEngine;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder;
use Wikibase\QueryEngine\SQLStore\Engine\Engine;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityInserter;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityRemover;
use Wikibase\QueryEngine\SQLStore\EntityStore\EntityUpdater;
use Wikibase\QueryEngine\SQLStore\Setup\Installer;
use Wikibase\QueryEngine\SQLStore\Setup\Uninstaller;
use Wikibase\QueryEngine\SQLStore\Setup\Updater;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRowBuilder;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;

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

	private $config;
	private $schema;

	public function __construct( StoreSchema $schema, StoreConfig $config ) {
		$this->schema = $schema;
		$this->config = $config;
	}

	/**
	 * @param Connection $connection
	 * @param PropertyDataValueTypeLookup $lookup
	 * @param EntityIdParser $idParser
	 *
	 * @return QueryEngine
	 */
	public function newQueryEngine( Connection $connection, PropertyDataValueTypeLookup $lookup,
		EntityIdParser $idParser ) {

		return new Engine(
			$this->newDescriptionMatchFinder( $connection, $lookup, $idParser )
		);
	}

	/**
	 * @param Connection $connection
	 *
	 * @return QueryStoreWriter
	 */
	public function newWriter( Connection $connection ) {
		return new Writer(
			$connection,
			$this->newEntityInserter( $connection ),
			$this->newEntityUpdater( $connection ),
			$this->newEntityRemover( $connection )
		);
	}

	public function newInstaller( AbstractSchemaManager $schemaManager ) {
		return new Installer(
			$this->config,
			$this->schema,
			$schemaManager
		);
	}

	public function newUninstaller( AbstractSchemaManager $schemaManager ) {
		return new Uninstaller(
			$this->config,
			$this->schema,
			$schemaManager
		);
	}

	public function newUpdater( AbstractSchemaManager $schemaManager ) {
		return new Updater(
			$this->schema,
			$schemaManager
		);
	}

	private function newEntityInserter( Connection $connection ) {
		return new EntityInserter(
			$this->newClaimInserter( $connection )
		);
	}

	private function newEntityUpdater( Connection $connection ) {
		return new EntityUpdater(
			$this->newEntityRemover( $connection ),
			$this->newEntityInserter( $connection )
		);
	}

	private function newEntityRemover( Connection $connection ) {
		return new EntityRemover(
			$this->newSnakRemover( $connection )
		);
	}

	private function newSnakRemover( Connection $connection ) {
		return new SnakRemover( $this->getSnakStores( $connection ) );
	}

	private function newClaimInserter( Connection $connection ) {
		return new ClaimInserter(
			$this->newSnakInserter( $connection ),
			new ClaimRowBuilder()
		);
	}

	private function newSnakInserter( Connection $connection ) {
		return new SnakInserter(
			$this->getSnakStores( $connection ),
			new SnakRowBuilder()
		);
	}

	/**
	 * @param Connection $connection
	 *
	 * @return SnakStore[]
	 */
	private function getSnakStores( Connection $connection ) {
		return array(
			new ValueSnakStore(
				$connection,
				$this->schema->getDataValueHandlers()->getMainSnakHandlers(),
				SnakRole::MAIN_SNAK
			),
			new ValueSnakStore(
				$connection,
				$this->schema->getDataValueHandlers()->getQualifierHandlers(),
				SnakRole::QUALIFIER
			),
			new ValuelessSnakStore(
				$connection,
				$this->schema->getValuelessSnaksTable()->getName()
			)
		);
	}

	private function newDescriptionMatchFinder( Connection $connection,
		PropertyDataValueTypeLookup $lookup, EntityIdParser $idParser ) {

		return new DescriptionMatchFinder(
			$connection,
			$this->schema,
			$lookup,
			$idParser
		);
	}

}
