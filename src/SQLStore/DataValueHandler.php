<?php

namespace Wikibase\QueryEngine\SQLStore;

use DataValues\DataValue;
use Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;
use RuntimeException;

/**
 * Represents the mapping between a DataValue type and the
 * associated implementation in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DataValueHandler {

	/**
	 * Needs to be set by the constructor.
	 *
	 * @var string|null
	 */
	protected $tableName = null;

	/**
	 * Returns the full name of the table.
	 * This is the same value as ->constructTable()->getName().
	 *
	 * @return string
	 *
	 * @throws RuntimeException
	 */
	public function getTableName() {
		if ( $this->tableName === null ) {
			throw new RuntimeException(
				'Cannot get the table name when the table name prefix has not been set yet'
			);
		}

		return $this->tableName;
	}

	/**
	 * Prefixes the table name. This needs to be called once, and only once,
	 * before getTableName or constructTable are called.
	 *
	 * @param string $tablePrefix
	 *
	 * @throws RuntimeException
	 */
	public function setTablePrefix( $tablePrefix ) {
		if ( $this->tableName !== null ) {
			throw new RuntimeException( 'Cannot set the table name prefix more than once' );
		}

		$this->tableName = $tablePrefix . $this->getBaseTableName();
	}

	/**
	 * Returns a Table object that represents the schema of the data value table.
	 *
	 * @return Table
	 */
	public function constructTable() {
		$table = new Table( $this->getTableName() );

		$this->completeTable( $table );

		return $table;
	}

	/**
	 * Returns the base name of the table.
	 * This does not contain any prefixes indicating which store it
	 * belongs to or what the role of the data value it handles is.
	 *
	 * @return string
	 */
	abstract protected function getBaseTableName();

	/**
	 * @param Table $table
	 */
	abstract protected function completeTable( Table $table );

	/**
	 * Returns the name of the field that holds the value from which
	 * a DataValue instance can be (re)constructed.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	abstract public function getValueFieldName();

	/**
	 * Returns the name of the field that holds a value suitable for equality checks.
	 *
	 * This field should not exceed 255 chars index space equivalent.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getEqualityFieldName() {
		return $this->getValueFieldName();
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
		return $this->getValueFieldName();
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
		return $this->getValueFieldName();
	}

	/**
	 * Create a DataValue from a cell value in the tables value field.
	 *
	 * @since 0.1
	 *
	 * @param string $valueFieldValue
	 *
	 * @return DataValue
	 *
	 * TODO: exception type
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

}
