<?php

namespace Wikibase\QueryEngine\SQLStore;

use DataValues\DataValue;
use InvalidArgumentException;

/**
 * Represents the mapping between a DataValue type and the
 * associated implementation in the store.
 *
 * Based on, and containing snippets from, SMWDataItemHandler from Semantic MediaWiki.
 * SMWDataItemHandler was written by Nischay Nahata and Markus Krötzsch.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DataValueHandler {

	/**
	 * @since 0.1
	 *
	 * @var DataValueTable
	 */
	protected $dataValueTable;

	/**
	 * @since 0.1
	 *
	 * @param DataValueTable $dataValueTable
	 */
	public function __construct( DataValueTable $dataValueTable ) {
		$this->dataValueTable = $dataValueTable;
	}

	/**
	 * Create a DataValue from a cell value in the tables value field.
	 *
	 * @since 0.1
	 *
	 * @param string $valueFieldValue
	 *
	 * @return DataValue
	 */
	abstract public function newDataValueFromValueField( $valueFieldValue );

	/**
	 * Return an array of fields=>values that is to be inserted when
	 * writing the given DataValue to the database. Values should be set
	 * for all columns, even if NULL. This array is used to perform all
	 * insert operations into the DB.
	 *
	 * The passed DataValue needs to be of a type supported by the DataValueHandler.
	 * If it is not supported, an InvalidArgumentException might be thrown.
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	abstract public function getInsertValues( DataValue $value );

	/**
	 * Returns the equality field value for a given data value.
	 * This value is needed for constructing equality checking
	 * queries.
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	abstract public function getEqualityFieldValue( DataValue $value );

	/**
	 * @since 0.1
	 *
	 * @return DataValueTable
	 */
	public function getDataValueTable() {
		return $this->dataValueTable;
	}

	/**
	 * @since 0.1
	 *
	 * @param DataValueTable $dvTable
	 */
	public function setDataValueTable( DataValueTable $dvTable ) {
		$this->dataValueTable = $dvTable;
	}

}
