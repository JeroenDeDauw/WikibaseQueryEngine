<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\QueryEngine\QueryEngine;
use Wikibase\QueryEngine\QueryStore;
use Wikibase\QueryEngine\QueryStoreSetup;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\Engine\Engine;

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
	 * @var Factory
	 */
	private $factory;

	/**
	 * @var TableBuilder
	 */
	private $tableBuilder;

	/**
	 * @since 0.1
	 *
	 * @param StoreConfig $config
	 * @param QueryInterface $queryInterface
	 * @param TableBuilder $tableBuilder
	 */
	public function __construct( StoreConfig $config, QueryInterface $queryInterface, TableBuilder $tableBuilder ) {
		$this->config = $config;
		$this->queryInterface = $queryInterface;
		$this->tableBuilder = $tableBuilder;

		$this->factory = new Factory( $config, $queryInterface );
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
			$this->factory->newDescriptionMatchFinder()
		);
	}

	/**
	 * @see QueryStore::getUpdater
	 *
	 * @since 0.1
	 *
	 * @return QueryStoreWriter
	 */
	public function getUpdater() {
		return $this->factory->newWriter();
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
			$this->config,
			$this->factory->getSchema(),
			$this->tableBuilder
			// TODO: add message reporter
		);
	}

}
