<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;
use OutOfBoundsException;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValueSnakStore extends SnakStore {

	protected $queryInterface;
	protected $dataValueHandlers;
	protected $snakRole;

	/**
	 * The array of DataValueHandlers must have DataValue types as array keys pointing to
	 * the corresponding DataValueHandler.
	 *
	 * @param QueryInterface $queryInterface
	 * @param DataValueHandler[] $dataValueHandlers
	 * @param int $supportedSnakRole
	 */
	public function __construct( QueryInterface $queryInterface, array $dataValueHandlers, $supportedSnakRole ) {
		$this->queryInterface = $queryInterface;
		$this->dataValueHandlers = $dataValueHandlers;
		$this->snakRole = $supportedSnakRole;
	}

	public function canStore( SnakRow $snakRow ) {
		return ( $snakRow instanceof ValueSnakRow )
			&& $this->snakRole === $snakRow->getSnakRole();
	}

	/**
	 * @param string $dataValueType
	 *
	 * @return DataValueHandler
	 * @throws OutOfBoundsException
	 */
	protected function getDataValueHandler( $dataValueType ) {
		if ( !array_key_exists( $dataValueType, $this->dataValueHandlers ) ) {
			throw new OutOfBoundsException( "There is no DataValueHandler set for '$dataValueType'" );
		}

		return $this->dataValueHandlers[$dataValueType];
	}

	public function storeSnakRow( SnakRow $snakRow ) {
		if ( !$this->canStore( $snakRow ) ) {
			throw new InvalidArgumentException( 'Can only store ValueSnakRow of the right snak type in ValueSnakStore' );
		}

		/**
		 * @var ValueSnakRow $snakRow
		 */
		if ( !array_key_exists( $snakRow->getValue()->getType(), $this->dataValueHandlers ) ) {
			return;
		}

		$dataValueHandler = $this->getDataValueHandler( $snakRow->getValue()->getType() );

		$tableName = $dataValueHandler->getDataValueTable()->getTableDefinition()->getName();

		$this->queryInterface->insert(
			$tableName,
			$this->getInsertValues( $snakRow, $dataValueHandler )
		);
	}

	private function getInsertValues( ValueSnakRow $snakRow, DataValueHandler $dataValueHandler ) {
		return array_merge(
			array(
				'subject_id' => $snakRow->getSubjectId()->getSerialization(),
				'subject_type' => $snakRow->getSubjectId()->getEntityType(),
				'property_id' => $snakRow->getPropertyId(),
				'statement_rank' => $snakRow->getStatementRank(),
			),
			$dataValueHandler->getInsertValues( $snakRow->getValue() )
		);
	}

	public function removeSnaksOfSubject( EntityId $subjectId ) {
		foreach ( $this->dataValueHandlers as $dvHandler ) {
			$this->queryInterface->delete(
				$dvHandler->getDataValueTable()->getTableDefinition()->getName(),
				array( 'subject_id' => $subjectId->getSerialization() )
			);
		}
	}

}
