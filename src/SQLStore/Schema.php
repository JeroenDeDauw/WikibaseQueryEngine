<?php

namespace Wikibase\QueryEngine\SQLStore;

use OutOfBoundsException;
use OutOfRangeException;
use Wikibase\Database\FieldDefinition;
use Wikibase\Database\TableDefinition;
use Wikibase\SnakRole;

/**
 * Contains the tables and table interactors for a given SQLStore configuration.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Schema {

	/**
	 * @since 0.1
	 *
	 * @var StoreConfig
	 */
	private $config;

	/**
	 * The DataValueHandlers for the DataValue types supported by this configuration.
	 * Array keys are snak types pointing to arrays where array keys are DataValue type
	 * identifiers (string) pointing to the corresponding DataValueHandler.
	 *
	 * @since 0.1
	 *
	 * @var array[]
	 */
	private $dvHandlers = array();

	/**
	 * int => str
	 *
	 * @since 0.1
	 *
	 * @var string[]
	 */
	private $snakTypes;

	/**
	 * @since 0.1
	 *
	 * @var boolean
	 */
	private $initialized = false;

	/**
	 * @since 0.1
	 *
	 * @param StoreConfig $config
	 */
	public function __construct( StoreConfig $config ) {
		$this->config = $config;

		$this->snakTypes = array(
			SnakRole::MAIN_SNAK => 'mainsnak_',
			SnakRole::QUALIFIER => 'qualifier_',
		);
	}

	/**
	 * Returns all tables part of the stores schema.
	 *
	 * @since 0.1
	 *
	 * @return TableDefinition[]
	 */
	public function getTables() {
		$this->initialize();

		return array_merge(
			$this->getNonDvTables(),
			$this->getDvTables()
		);
	}

	/**
	 * Returns the DataValueHandler for a given DataValue type and SnakRole.
	 *
	 * @since 0.1
	 *
	 * @param string $dataValueType
	 * @param int $snakRole
	 *
	 * @return DataValueHandler
	 * @throws OutOfBoundsException
	 */
	public function getDataValueHandler( $dataValueType, $snakRole ) {
		$dataValueHandlers = $this->getDataValueHandlers( $snakRole );

		if ( !array_key_exists( $dataValueType, $dataValueHandlers ) ) {
			throw new OutOfBoundsException(
				'Requested a DataValuerHandler for DataValue type '
					. "'$dataValueType' while no handler for this type is set"
			);
		}

		return $dataValueHandlers[$dataValueType];
	}

	/**
	 * @since 0.1
	 *
	 * @param int $snakRole
	 *
	 * @return DataValueHandler[]
	 * @throws OutOfRangeException
	 */
	public function getDataValueHandlers( $snakRole ) {
		$this->initialize();

		if ( !array_key_exists( $snakRole, $this->dvHandlers ) ) {
			throw new OutOfRangeException( 'Got an unsupported snak role' );
		}

		return $this->dvHandlers[$snakRole];
	}

	/**
	 * @since 0.1
	 */
	private function initialize() {
		if ( $this->initialized ) {
			return;
		}

		$this->expandDataValueHandlers();

		$this->initialized = true;
	}

	/**
	 * Turns the list of DataValueHandler objects into a list of these objects per snak type.
	 * The table names are prefixed with both the stores table prefix and the snak type specific one.
	 * Additional fields required by the store are also added to the tables.
	 *
	 * @since 0.1
	 */
	private function expandDataValueHandlers() {
		foreach ( $this->snakTypes as $snakType => $snakTablePrefix ) {
			$handlers = array();

			foreach ( $this->config->getDataValueHandlers() as $dataValueType => $dataValueHandler ) {
				$dvTable = $dataValueHandler->getDataValueTable();

				$table = $dvTable->getTableDefinition();
				$table = $table->mutateName( $this->config->getTablePrefix() . $snakTablePrefix . $table->getName() );
				$table = $table->mutateFields(
					array_merge(
						$this->getPropertySnakFields(),
						$table->getFields()
					)
				);

				$dvTable = $dvTable->mutateTableDefinition( $table );
				$dataValueHandler = $dataValueHandler->mutateDataValueTable( $dvTable );

				$handlers[$dataValueType] = $dataValueHandler;
			}

			$this->dvHandlers[$snakType] = $handlers;
		}
	}

	/**
	 * @since 0.1
	 *
	 * @return FieldDefinition[]
	 */
	private function getPropertySnakFields() {
		// TODO: indexes

		return array(
			// Internal subject id
			new FieldDefinition(
				'subject_id',
				FieldDefinition::TYPE_INTEGER,
				FieldDefinition::NOT_NULL,
				FieldDefinition::NO_DEFAULT,
				FieldDefinition::ATTRIB_UNSIGNED
			),

			// Internal property id
			new FieldDefinition(
				'property_id',
				FieldDefinition::TYPE_INTEGER,
				FieldDefinition::NOT_NULL,
				FieldDefinition::NO_DEFAULT,
				FieldDefinition::ATTRIB_UNSIGNED
			),
		);
	}

	/**
	 * @since 0.1
	 *
	 * @return TableDefinition[]
	 */
	private function getDvTables() {
		$tables = array();

		foreach ( $this->dvHandlers as $dvHandlers ) {
			/**
			 * @var DataValueHandler $dvHandler
			 */
			foreach ( $dvHandlers as $dvHandler ) {
				$tables[] = $dvHandler->getDataValueTable()->getTableDefinition();
			}
		}

		return $tables;
	}

	/**
	 * @since 0.1
	 *
	 * @return TableDefinition
	 */
	public function getEntitiesTable() {
		// TODO: indexes
		return new TableDefinition(
			$this->config->getTablePrefix() . 'entities',
			array(
				// Internal id
				new FieldDefinition(
					'id',
					FieldDefinition::TYPE_INTEGER,
					FieldDefinition::NOT_NULL,
					FieldDefinition::NO_DEFAULT,
					FieldDefinition::ATTRIB_UNSIGNED
				),

				// EntityId type part
				new FieldDefinition(
					'type',
					FieldDefinition::TYPE_TEXT,
					FieldDefinition::NOT_NULL,
					FieldDefinition::NO_DEFAULT,
					FieldDefinition::NO_ATTRIB
				),

				// EntityId numerical part
				new FieldDefinition(
					'number',
					FieldDefinition::TYPE_INTEGER,
					FieldDefinition::NOT_NULL,
					FieldDefinition::NO_DEFAULT,
					FieldDefinition::ATTRIB_UNSIGNED
				),
			)
		);
	}

	/**
	 * @since 0.1
	 *
	 * @return TableDefinition
	 */
	public function getValuelessSnaksTable() {
		// TODO: indexes
		return new TableDefinition(
			$this->config->getTablePrefix() . 'valueless_snaks',
			array_merge(
				$this->getPropertySnakFields(),
				array(
					 // Type of the snak
					 new FieldDefinition(
						 'snak_type',
						 FieldDefinition::TYPE_INTEGER,
						 FieldDefinition::NOT_NULL,
						 FieldDefinition::NO_DEFAULT,
						 FieldDefinition::ATTRIB_UNSIGNED
					 ),

					 // Role of the snak (ie "main snak" or "qualifier")
					 new FieldDefinition(
						 'snak_role',
						 FieldDefinition::TYPE_INTEGER,
						 FieldDefinition::NOT_NULL,
						 FieldDefinition::NO_DEFAULT,
						 FieldDefinition::ATTRIB_UNSIGNED
					 ),
				)
			)
		);
	}

	/**
	 * @since 0.1
	 *
	 * @return TableDefinition[]
	 */
	private function getNonDvTables() {
		$tables = array();

		// TODO: multi field indexes
		// TODO: more optimal types

		// Id map with Wikibase EntityId to internal SQL store id
		$tables[] = $this->getEntitiesTable();

		// Table for snaks without a value
		$tables[] = $this->getValuelessSnaksTable();

		return $tables;
	}

}