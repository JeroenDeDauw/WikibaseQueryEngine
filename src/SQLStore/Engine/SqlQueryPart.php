<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

/**
 * @private
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SqlQueryPart {

	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * @var string[]
	 */
	private $selectParts = [];

	/**
	 * @var string[]
	 */
	private $sortFields = [];

	/**
	 * @var string[]
	 */
	private $whereParts = [];

	/**
	 * @var string[]
	 */
	private $parameters = [];

	/**
	 * @param string $tableName
	 */
	public function setTableName( $tableName ) {
		$this->tableName = $tableName;
	}

	/**
	 * @param string[] $selectParts
	 */
	public function setSelectParts( array $selectParts ) {
		$this->selectParts = $selectParts;
	}

	/**
	 * @param string $where
	 */
	public function andWhere( $where ) {
		$this->whereParts[] = $where;
	}

	/**
	 * @param string[] $sortFields
	 */
	public function setSortFields( $sortFields ) {
		$this->sortFields = $sortFields;
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * @return string[]
	 */
	public function getSelectParts() {
		return $this->selectParts;
	}

	/**
	 * @return string[]
	 */
	public function getWhereParts() {
		return $this->whereParts;
	}

	/**
	 * @return string[]
	 */
	public function getSortFields() {
		return $this->sortFields;
	}

	/**
	 * @param string $parameterName
	 * @param string $value
	 */
	public function setParameter( $parameterName, $value ) {
		$this->parameters[$parameterName] = $value;
	}

}