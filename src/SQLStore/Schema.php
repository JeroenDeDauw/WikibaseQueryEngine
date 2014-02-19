<?php

namespace Wikibase\QueryEngine\SQLStore;

use OutOfBoundsException;
use OutOfRangeException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\DataModel\Snak\SnakRole;

/**
 * Contains the tables and table interactors for a given SQLStore configuration.
 *
 * @since 0.1
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
	 */
	private function expandDataValueHandlers() {
		foreach ( $this->snakTypes as $snakType => $snakTablePrefix ) {
			$handlers = array();

			foreach ( $this->config->getDataValueHandlers() as $dataValueType => $dataValueHandler ) {
				$handlers[$dataValueType] = $this->expandDataValueHandler( $dataValueHandler, $snakTablePrefix );
			}

			$this->dvHandlers[$snakType] = $handlers;
		}
	}

	private function expandDataValueHandler( DataValueHandler $dataValueHandler, $snakTablePrefix ) {
		$dvTable = $dataValueHandler->getDataValueTable();

		$table = $dvTable->getTableDefinition();
		$table = $table->mutateName( $this->config->getTablePrefix() . $snakTablePrefix . $table->getName() );
		$table = $table->mutateFields(
			array_merge(
				$this->getPropertySnakFields(),
				$table->getFields()
			)
		);

		/** @var TableDefinition $table */
		$table = $table->mutateIndexes(
			array_merge(
				$this->getCommonPropertySnakIndexes(),
				array(
					new IndexDefinition(
						'value_property',
						array(
							$dvTable->getEqualityFieldName() => 0,
							'property_id' => 16,
							'entity_id' => 16,
						),
						IndexDefinition::TYPE_UNIQUE
					),
				),
				$table->getIndexes()
			)
		);

		$dvTable = $dvTable->mutateTableDefinition( $table );
		$dataValueHandler = $dataValueHandler->mutateDataValueTable( $dvTable );

		return $dataValueHandler;
	}

	/**
	 * @since 0.1
	 *
	 * @return FieldDefinition[]
	 */
	private function getPropertySnakFields() {
		return array(
			new FieldDefinition(
				'row_id',
				new TypeDefinition(
					TypeDefinition::TYPE_BIGINT
				),
				FieldDefinition::NOT_NULL,
				FieldDefinition::NO_DEFAULT,
				FieldDefinition::AUTOINCREMENT
			),

			new FieldDefinition(
				'entity_type',
				new TypeDefinition(
					TypeDefinition::TYPE_VARCHAR,
					8
				),
				FieldDefinition::NOT_NULL
			),

			new FieldDefinition(
				'entity_id',
				new TypeDefinition(
					TypeDefinition::TYPE_VARCHAR,
					16
				),
				FieldDefinition::NOT_NULL
			),

			new FieldDefinition(
				'property_id',
				new TypeDefinition(
					TypeDefinition::TYPE_VARCHAR,
					16
				),
				FieldDefinition::NOT_NULL
			),

			new FieldDefinition(
				'statement_rank',
				new TypeDefinition(
					TypeDefinition::TYPE_TINYINT
				),
				FieldDefinition::NOT_NULL
			),
		);
	}

	/**
	 * @since 0.1
	 *
	 * @return IndexDefinition[]
	 */
	private function getCommonPropertySnakIndexes() {
		return array(
			new IndexDefinition(
				'PRIMARY',
				array( 'row_id' => 0 ),
				IndexDefinition::TYPE_PRIMARY
			),
			new IndexDefinition(
				'entity_id',
				array( 'entity_id' => 16, ),
				IndexDefinition::TYPE_INDEX
			),

			new IndexDefinition(
				'property_id',
				array( 'property_id' => 16, ),
				IndexDefinition::TYPE_INDEX
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
		return new TableDefinition(
			$this->config->getTablePrefix() . 'entities',
			array(
				new FieldDefinition(
					'id',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						16
					),
					FieldDefinition::NOT_NULL
				),

				// Entity type
				new FieldDefinition(
					'type',
					new TypeDefinition(
						TypeDefinition::TYPE_VARCHAR,
						16
					),
					FieldDefinition::NOT_NULL
				),
			),
			array(
				new IndexDefinition(
					'PRIMARY',
					array( 'id' => 16 ),
					IndexDefinition::TYPE_PRIMARY
				),
				new IndexDefinition(
					'type',
					array( 'type' => 16 ),
					IndexDefinition::TYPE_INDEX
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
		return new TableDefinition(
			$this->config->getTablePrefix() . 'valueless_snaks',
			array_merge(
				$this->getPropertySnakFields(),
				array(
					 // Type of the snak
					 new FieldDefinition(
						'snak_type',
						 new TypeDefinition(
							 TypeDefinition::TYPE_INTEGER,
							 TypeDefinition::NO_SIZE,
							 TypeDefinition::ATTRIB_UNSIGNED
						 ),
						FieldDefinition::NOT_NULL,
						FieldDefinition::NO_DEFAULT
					 ),
				)
			),
			$this->getCommonPropertySnakIndexes()
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