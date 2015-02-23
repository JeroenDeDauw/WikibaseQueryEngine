<?php

namespace Wikibase\QueryEngine\SQLStore;

/**
 * @private
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WhereConditions {

	/**
	 * @var string[]
	 */
	private $conditions = [];

	/**
	 * @var array
	 */
	private $parameters = [];

	/**
	 * Adds a condition. Embedded values should either be safe,
	 * or referred to as parameter (ie :paramName) and then
	 * have their value set via @see setParameter
	 *
	 * Same contract as Doctrine DBALs QueryBuilder::andWhere
	 *
	 * @param string $whereString
	 */
	public function addCondition( $whereString ) {
		$this->conditions[] = $whereString;
	}

	/**
	 * @param string $parameterName Should be prefixed with a colon
	 * @param mixed $value The raw (non escaped) value
	 */
	public function setParameter( $parameterName, $value ) {
		$this->parameters[$parameterName] = $value;
	}

	/**
	 * Creates and adds an equality condition, where the value
	 * gets referred to as parameter :$fieldName.
	 * Also sets the parameter :$fieldName, and can thus override
	 * an existing parameter.
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 */
	public function setEquality( $fieldName, $value ) {
		$this->conditions[] = $fieldName . ' = :' . $fieldName;
		$this->parameters[':' . $fieldName] = $value;
	}

	/**
	 * @return string[]
	 */
	public function getConditions() {
		return $this->conditions;
	}

	/**
	 * Array of parameter name => value. Parameter names
	 * do not have a preceding colon and values are not
	 * escaped.
	 *
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}

}