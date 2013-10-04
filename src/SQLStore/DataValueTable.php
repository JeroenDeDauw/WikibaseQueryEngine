<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\Schema\Definitions\TableDefinition;

/**
 * Declaration of a table in which data values can be stored.
 *
 * Based on, and containing snippets from, SMWDataItemHandler from Semantic MediaWiki.
 * SMWDataItemHandler was written by Nischay Nahata and Markus Krötzsch.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class DataValueTable implements \Immutable {

	/**
	 * @since 0.1
	 *
	 * @var TableDefinition
	 */
	private $tableDefinition;

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $valueFieldName;

	/**
	 * @since 0.1
	 *
	 * @var string
	 */
	private $sortFieldName;

	/**
	 * @since 0.1
	 *
	 * @var null|string
	 */
	private $labelFieldName;

	/**
	 * @since 0.1
	 *
	 * @param TableDefinition $table
	 * @param string $valueFieldName
	 * @param string $sortFieldName
	 * @param string|null $labelFieldName
	 */
	public function __construct( TableDefinition $table, $valueFieldName, $sortFieldName, $labelFieldName = null ) {
		$this->tableDefinition = $table;
		$this->valueFieldName = $valueFieldName;
		$this->sortFieldName = $sortFieldName;
		$this->labelFieldName = $labelFieldName;
	}

	/**
	 * Returns the name of the field that holds the value from which
	 * a DataValue instance can be (re)constructed.
	 *
	 * The field should clearly be part of the table returned
	 * by @see getTableDefinition.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getValueFieldName() {
		return $this->valueFieldName;
	}

	/**
	 * Return the field used to select this type of DataValue. In
	 * particular, this identifies the column that is used to sort values
	 * of this kind. Every type of data returns a non-empty string here.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getSortFieldName() {
		return $this->sortFieldName;
	}

	/**
	 * Return the label field for this type of DataValue. This should be
	 * a string column in the database table that can be used for selecting
	 * values using criteria such as "starts with". The return value can be
	 * empty if this is not supported. This is preferred for DataValue
	 * classes that do not have an obvious canonical string writing anyway.
	 *
	 * The return value can be a column name or the empty string (if the
	 * give type of DataValue does not have a label field).
	 *
	 * @since 0.1
	 *
	 * @return string|null
	 */
	public function getLabelFieldName() {
		return $this->labelFieldName;
	}

	/**
	 * @since 0.1
	 *
	 * @return TableDefinition
	 */
	public function getTableDefinition() {
		return $this->tableDefinition;
	}

	/**
	 * Returns a clone of the DataValue table, though with the provided table definition rather then the original one.
	 *
	 * @since wd.db
	 *
	 * @param TableDefinition $tableDefinition
	 *
	 * @return DataValueTable
	 */
	public function mutateTableDefinition( TableDefinition $tableDefinition ) {
		return new self( $tableDefinition, $this->valueFieldName, $this->sortFieldName, $this->labelFieldName );
	}

}
