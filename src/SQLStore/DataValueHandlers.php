<?php

namespace Wikibase\QueryEngine\SQLStore;

use OutOfBoundsException;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DataValueHandlers {

	/**
	 * @var DataValueHandler[]
	 */
	private $mainSnakHandlers = [];

	/**
	 * @var DataValueHandler[]
	 */
	private $qualifierHandlers = [];

	public function addMainSnakHandler( $dataTypeId, DataValueHandler $handler ) {
		$this->mainSnakHandlers[$dataTypeId] = $handler;
	}

	public function addQualifierHandler( $dataTypeId, DataValueHandler $handler ) {
		$this->qualifierHandlers[$dataTypeId] = $handler;
	}

	/**
	 * @param string $dataTypeId
	 *
	 * @return DataValueHandler
	 * @throws OutOfBoundsException
	 */
	public function getMainSnakHandler( $dataTypeId ) {
		if ( !array_key_exists( $dataTypeId, $this->mainSnakHandlers ) ) {
			throw new OutOfBoundsException( "There is no main snak DataValueHandler for '$dataTypeId'." );
		}

		return $this->mainSnakHandlers[$dataTypeId];
	}

	/**
	 * @param string $dataTypeId
	 *
	 * @return DataValueHandler
	 * @throws OutOfBoundsException
	 */
	public function getQualifierHandler( $dataTypeId ) {
		if ( !array_key_exists( $dataTypeId, $this->qualifierHandlers ) ) {
			throw new OutOfBoundsException( "There is no qualifier DataValueHandler for '$dataTypeId'." );
		}

		return $this->qualifierHandlers[$dataTypeId];
	}

	/**
	 * @return DataValueHandler[]
	 */
	public function getMainSnakHandlers() {
		return $this->mainSnakHandlers;
	}

	/**
	 * @return DataValueHandler[]
	 */
	public function getQualifierHandlers() {
		return $this->qualifierHandlers;
	}

}
