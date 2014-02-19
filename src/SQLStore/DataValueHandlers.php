<?php

namespace Wikibase\QueryEngine\SQLStore;

use OutOfBoundsException;
use Wikibase\QueryEngine\SQLStore\DVHandler\BooleanHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\EntityIdHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\LatLongHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\MonolingualTextHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;

/**
 * A collection of DataValueHandler objects to be used by the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
final class DataValueHandlers {

	/**
	 * @since 0.1
	 *
	 * @var DataValueHandler[]
	 */
	private $dvHandlers;

	/**
	 * @since 0.1
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * Returns all DataValueHandler objects.
	 * Array keys are data value types pointing to the corresponding DataValueHandler.
	 *
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	public function getHandlers() {
		$this->initialize();

		return $this->dvHandlers;
	}

	/**
	 * Returns the DataValueHandler for the specified DataValue type.
	 * Note that this is not suited for interaction with the actual store schema,
	 * for that one should use the Schema class.
	 *
	 * @since 0.1
	 *
	 * @param string $dataValueType
	 *
	 * @return DataValueHandler
	 * @throws OutOfBoundsException
	 */
	public function getHandler( $dataValueType ) {
		$this->initialize();

		if ( !array_key_exists( $dataValueType, $this->dvHandlers ) ) {
			throw new OutOfBoundsException( "There is no DataValueHandler registered for DataValue type '$dataValueType'" );
		}

		return $this->dvHandlers[$dataValueType];
	}

	/**
	 * Initialize the object if not done so already.
	 *
	 * @since 0.1
	 */
	private function initialize() {
		if ( $this->initialized ) {
			return;
		}

		$this->dvHandlers = $this->getDefaultHandlers();

		$this->initialized = true;
	}

	/**
	 * @since 0.1
	 *
	 * @return DataValueTable[]
	 */
	private function getDefaultHandlers() {
		$tables = array();

		$tables['boolean'] = new BooleanHandler();

		$tables['string'] = new StringHandler();

		$tables['monolingualtext'] = new MonolingualTextHandler();

		$tables['latlong'] = new LatLongHandler();

		$tables['number'] = new NumberHandler();

		// TODO: register via hook
		$tables['wikibase-entityid'] = new EntityIdHandler();

		//TODO wbq_<role>_time table

		return $tables;
	}

}
