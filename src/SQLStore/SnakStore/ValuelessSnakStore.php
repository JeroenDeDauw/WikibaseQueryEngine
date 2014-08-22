<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValuelessSnakStore extends SnakStore {

	private $connection;
	private $tableName;

	public function __construct( Connection $connection, $tableName ) {
		$this->connection = $connection;
		$this->tableName = $tableName;
	}

	public function canStore( SnakRow $snakRow ) {
		return $snakRow instanceof ValuelessSnakRow;
	}

	public function storeSnakRow( SnakRow $snakRow ) {
		if ( !$this->canStore( $snakRow ) ) {
			throw new InvalidArgumentException( 'Can only store ValuelessSnakRow in ValuelessSnakStore' );
		}

		/**
		 * @var ValuelessSnakRow $snakRow
		 */
		$this->connection->insert(
			$this->tableName,
			array(
				'subject_id' => $snakRow->getSubjectId()->getSerialization(),
				'subject_type' => $snakRow->getSubjectId()->getEntityType(),
				'property_id' => $snakRow->getPropertyId(),
				'statement_rank' => $snakRow->getStatementRank(),
				'snak_type' => $snakRow->getInternalSnakType(),
			)
		);
	}

	public function removeSnaksOfSubject( EntityId $subjectId ) {
		$this->connection->delete(
			$this->tableName,
			array( 'subject_id' => $subjectId->getSerialization() )
		);
	}

}
