<?php

namespace Wikibase\QueryEngine;

use Doctrine\DBAL\Query\QueryBuilder;

class DBALQueryBuilder extends QueryBuilder {
	/**
	 * @param string[] $columnNames
	 * @param string   $tableName
	 * @param array    $criteria [$columnName (string) => $columnValue (mixed)]
	 *
	 * @return self
	 */
	public function selectFromWhere(array $columnNames, $tableName, array $criteria) {
		$this
			->selectFromTable($columnNames, 't')
			->whereValueFromTable($criteria, 't')
			->from($tableName, 't');

		return $this;
	}

	/**
	 * @param string[] $columnNames
	 * @param string   $tableName
	 *
	 * @return self
	 */
	public function selectFromTable(array $columnNames, $tableName) {
		$this->select(array_map(
			function($columnName) use ($tableName)
				{
					return $tableName . '.' . $columnName;
				},
			$columnNames
		));

		return $this;
	}

	/**
	 * @param array  $criteria [$columnName (string) => $columnValue (mixed)]
	 * @param string $tableName
	 *
	 * @return self
	 */
	public function whereValueFromTable(array $criteria, $tableName) {
		$wherePredicates = array();

		foreach ($criteria as $columnName => $columnValue) {
			$wherePredicates[] = $tableName . '.' . $columnName . ' = :' . $columnName;
			$this->setParameter(':' . $columnName, $columnValue);
		}

		$this->where(implode(' AND ', $wherePredicates));

		return $this;
	}
}