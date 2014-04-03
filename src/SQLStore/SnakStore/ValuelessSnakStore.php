<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValuelessSnakStore extends SnakStore {

	protected $queryInterface;
	protected $tableName;

	public function __construct( QueryInterface $queryInterface, $tableName ) {
		$this->queryInterface = $queryInterface;
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
		$this->queryInterface->insert(
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
		$this->queryInterface->delete(
			$this->tableName,
			array( 'subject_id' => $subjectId->getSerialization() )
		);
	}

}
