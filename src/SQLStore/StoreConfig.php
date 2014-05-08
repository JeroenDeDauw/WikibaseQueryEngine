<?php

namespace Wikibase\QueryEngine\SQLStore;

/**
 * Configuration for the SQL Store.
 * This is purely a value object containing the configuration declaration.
 * Access to things config specific (such as the database tables) should
 * happen through specific objects (such as the Schema class).
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreConfig {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $storeName
	 */
	public function __construct( $storeName ) {
		$this->name = $storeName;
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
