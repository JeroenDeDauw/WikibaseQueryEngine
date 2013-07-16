<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\QueryInterface;
use Wikibase\Entity;

/**
 * Use case for inserting entities into the store.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityTable {

	private $queryInterface;
	private $entityTableName;

	/**
	 * @since 0.1
	 *
	 * @param QueryInterface $queryInterface
	 * @param string $entityTableName
	 */
	public function __construct( QueryInterface $queryInterface, $entityTableName ) {
		$this->queryInterface = $queryInterface;
		$this->entityTableName = $entityTableName;
	}

	/**
	 * @see QueryStoreUpdater::insertEntity
	 *
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function insertEntity( Entity $entity ) {
		$this->queryInterface->insert(
			$this->entityTableName,
			array(
				'type' => $entity->getType(),
				'number' => $entity->getId()->getNumericId(),
			)
		);
	}


	/**
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function removeEntity( Entity $entity ) {

	}

}
