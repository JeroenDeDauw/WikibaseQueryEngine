<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\MessageReporter;
use Wikibase\Database\QueryInterface;
use Wikibase\QueryEngine\QueryStore;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder;
use Wikibase\QueryEngine\SQLStore\Engine\Engine;

/**
 * Simple query store for relational SQL databases.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Store implements QueryStore {

	/**
	 * @since 0.1
	 *
	 * @var StoreConfig
	 */
	private $config;

	/**
	 * @since 0.1
	 *
	 * @var QueryInterface
	 */
	private $queryInterface;

	/**
	 * @since 0.1
	 *
	 * @var Factory
	 */
	private $factory;

	/**
	 * @since 0.1
	 *
	 * @param StoreConfig $config
	 * @param QueryInterface $queryInterface
	 */
	public function __construct( StoreConfig $config, QueryInterface $queryInterface ) {
		$this->config = $config;
		$this->queryInterface = $queryInterface;
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
	 * @return \Wikibase\QueryEngine\QueryEngine
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
	 * @return \Wikibase\QueryEngine\QueryStoreWriter
	 */
	public function getUpdater() {
		return $this->factory->newWriter();
	}

	/**
	 * @see QueryStore::newSetup
	 *
	 * @since 0.1
	 *
	 * @return Setup
	 */
	public function newSetup() {
		return new Setup(
			$this->config,
			$this->factory->getSchema(),
			$this->queryInterface,
			$this->factory->getTableBuilder()
		);
	}

}
