<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\DataModel\Entity\Entity;

/**
 * Use case for inserting entities into the store.
 *
 * @since 0.1
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
				'id' => $entity->getId()->getSerialization(),
				'type' => $entity->getType(),
			)
		);
	}

	/**
	 * @since 0.1
	 *
	 * @param Entity $entity
	 */
	public function removeEntity( Entity $entity ) {
		$this->queryInterface->delete(
			$this->entityTableName,
			array(
				'id' => $entity->getId()->getSerialization()
			)
		);
	}

}
