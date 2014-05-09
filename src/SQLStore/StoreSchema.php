<?php

namespace Wikibase\QueryEngine\SQLStore;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * Contains the tables and table interactors for a given SQLStore configuration.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreSchema {

	private $tablePrefix;
	private $dvHandlers;

	/**
	 * @param string $tablePrefix
	 * @param DataValueHandlers $dataValueHandlers
	 */
	public function __construct( $tablePrefix, DataValueHandlers $dataValueHandlers ) {
		$this->tablePrefix = $tablePrefix;
		$this->dvHandlers = $dataValueHandlers;

		foreach ( $this->dvHandlers->getMainSnakHandlers() as $dvHandler ) {
			$dvHandler->setTablePrefix( $this->tablePrefix . 'mainsnak_' );
		}

		foreach ( $this->dvHandlers->getQualifierHandlers() as $dvHandler ) {
			$dvHandler->setTablePrefix( $this->tablePrefix . 'qualifier_' );
		}
	}

	/**
	 * @return DataValueHandlers
	 */
	public function getDataValueHandlers() {
		return $this->dvHandlers;
	}

	/**
	 * Returns all tables part of the stores schema.
	 *
	 * @since 0.1
	 *
	 * @return Table[]
	 */
	public function getTables() {
		return array_merge(
			$this->getDvTables(),
			array(
				$this->getValuelessSnaksTable()
			)
		);
	}

	/**
	 * @return Table[]
	 */
	private function getDvTables() {
		$tables = array();

		foreach ( $this->dvHandlers->getMainSnakHandlers() as $dvHandler ) {
			$tables[] = $this->getTableFromDvHandler( $dvHandler );
		}

		foreach ( $this->dvHandlers->getQualifierHandlers() as $dvHandler ) {
			$tables[] = $this->getTableFromDvHandler( $dvHandler );
		}

		return $tables;
	}

	private function getTableFromDvHandler( DataValueHandler $handler ) {
		$table = $handler->constructTable();

		$this->addCommonColumnsToTable( $table );
		$this->addCommonIndexesToTable( $table );

		$table->addUniqueIndex( array(
			$handler->getEqualityFieldName(),
			'property_id',
			'subject_id',
		) );

		return $table;
	}

	private function addCommonColumnsToTable( Table $table ) {
		$table->addColumn(
			'row_id',
			Type::INTEGER,
			array(
				'autoincrement' => true,
				'unsigned' => true,
			)
		);

		$table->addColumn( 'subject_id', Type::STRING, array( 'length' => 16 ) );
		$table->addColumn( 'subject_type', Type::STRING, array( 'length' => 8 ) );
		$table->addColumn( 'property_id', Type::STRING, array( 'length' => 16 ) );
		$table->addColumn( 'statement_rank', Type::SMALLINT );
	}

	private function addCommonIndexesToTable( Table $table ) {
		$table->setPrimaryKey( array( 'row_id' ) );
		$table->addIndex( array( 'subject_id' ) );
		$table->addIndex( array( 'property_id' ) );
	}

	/**
	 * @since 0.1
	 *
	 * @return Table
	 */
	public function getValuelessSnaksTable() {
		$table = new Table( $this->tablePrefix . 'valueless_snaks' );

		$table->addColumn( 'snak_type', Type::INTEGER, array( 'unsigned' => true ) );

		$this->addCommonColumnsToTable( $table );
		$this->addCommonIndexesToTable( $table );

		return $table;
	}


}