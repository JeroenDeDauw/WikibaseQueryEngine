<?php

namespace Wikibase\QueryEngine\SQLStore;

use Exception;
use Wikibase\Database\TableDefinition;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;

/**
 * Configuration for the SQL Store.
 * This is purely a value object containing the configuration declaration.
 * Access to things config specific (such as the database tables) should
 * happen through specific objects (such as the Schema class).
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreConfig {

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $tablePrefix;

	/**
	 * The DataValueHandlers for the DataValue types supported by this configuration.
	 * Array keys are DataValue type identifiers (string) pointing to the corresponding DataValueHandler.
	 *
	 * @since 0.1
	 *
	 * @var DataValueHandler[]
	 */
	private $dvHandlers = array();

	/**
	 * @since 0.1
	 *
	 * @var PropertyDataValueTypeLookup|null
	 */
	protected $propertyDataValueTypeLookup = null;

	/**
	 * @since 0.1
	 *
	 * @param string $storeName
	 * @param string $tablePrefix
	 * @param DataValueHandler[] $dataValueHandlers
	 */
	public function __construct( $storeName, $tablePrefix, array $dataValueHandlers ) {
		$this->name = $storeName;
		$this->tablePrefix = $tablePrefix;
		$this->dvHandlers = $dataValueHandlers;
	}

	public function setPropertyDataValueTypeLookup( PropertyDataValueTypeLookup $lookup ) {
		$this->propertyDataValueTypeLookup = $lookup;
	}

	/**
	 * @return PropertyDataValueTypeLookup
	 *
	 * @throws Exception
	 */
	public function getPropertyDataValueTypeLookup() {
		if ( $this->propertyDataValueTypeLookup === null ) {
			throw new Exception( 'setPropertyDataValueTypeLookup has not been called yet' );
		}

		return $this->propertyDataValueTypeLookup;
	}

	/**
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getStoreName() {
		return $this->name;
	}

	/**
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getTablePrefix() {
		return $this->tablePrefix;
	}

	/**
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	public function getDataValueHandlers() {
		return $this->dvHandlers;
	}

	/**
	 * Returns a map that maps entity type (string) to internal id postfix digit (int, unique).
	 *
	 * @since 0.1
	 *
	 * @return int[]
	 */
	public function getEntityTypeMap() {
		return array(
			'item' => 0,
			'property' => 1,
		);
	}

}
