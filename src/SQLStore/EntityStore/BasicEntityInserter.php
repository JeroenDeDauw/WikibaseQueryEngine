<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BasicEntityInserter implements EntityInsertionStrategy {

	private $snakInserter;

	public function __construct( SnakInserter $snakInserter) {
		$this->snakInserter = $snakInserter;
	}

	public function insertEntity( EntityDocument $entity ) {
		if ( !$this->canInsert( $entity ) || !method_exists( $entity, 'getStatements' ) ) {
			throw new QueryEngineException( 'BasicEntityInserter got a type of entity it does not support' );
		}

		/**
		 * @var Item|Property $entity
		 */
		$statements = $this->getBestStatementsPerProperty( $entity->getStatements() );
		$this->insertStatements( $statements->getWithUniqueMainSnaks(), $entity->getId() );
	}

	private function getBestStatementsPerProperty( StatementList $statements ) {
		$bestStatements = new StatementList();

		foreach ( $statements->getPropertyIds() as $propertyId ) {
			foreach ( $statements->getByPropertyId( $propertyId )->getBestStatements() as $statement ) {
				$bestStatements->addStatement( $statement );
			}
		}

		return $bestStatements;
	}

	private function insertStatements( StatementList $statements, EntityId $subjectId ) {
		foreach ( $statements as $statement ) {
			$this->insertStatement( $statement, $subjectId );
		}
	}

	private function insertStatement( Statement $statement, EntityId $subjectId ) {
		$this->insertSnak( $statement->getMainSnak(), SnakRole::MAIN_SNAK, $subjectId, $statement->getRank() );

		foreach ( $statement->getQualifiers() as $qualifier ) {
			$this->insertSnak( $qualifier, SnakRole::QUALIFIER, $subjectId, $statement->getRank() );
		}
	}

	private function insertSnak( Snak $snak, $snakRole, EntityId $subjectId, $statementRank ) {
		$this->snakInserter->insertSnak( $snak, $snakRole, $subjectId, $statementRank );
	}

	/**
	 * @param EntityDocument $entity
	 *
	 * @return boolean
	 */
	public function canInsert( EntityDocument $entity ) {
		return $entity->getType() === 'item' || $entity->getType() === 'property';
	}

}
