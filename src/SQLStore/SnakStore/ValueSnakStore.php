<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use OutOfBoundsException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValueSnakStore extends SnakStore {

	private $connection;
	private $dataValueHandlers;
	private $snakRole;

	/**
	 * The array of DataValueHandlers must have DataValue types as array keys pointing to
	 * the corresponding DataValueHandler.
	 *
	 * @param Connection $connection
	 * @param DataValueHandler[] $dataValueHandlers
	 * @param int $supportedSnakRole
	 */
	public function __construct( Connection $connection, array $dataValueHandlers, $supportedSnakRole ) {
		$this->connection = $connection;
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
	private function getDataValueHandler( $dataValueType ) {
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

		$tableName = $dataValueHandler->getTableName();

		$this->connection->insert(
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
			$this->connection->delete(
				$dvHandler->getTableName(),
				array( 'subject_id' => $subjectId->getSerialization() )
			);
		}
	}

}
