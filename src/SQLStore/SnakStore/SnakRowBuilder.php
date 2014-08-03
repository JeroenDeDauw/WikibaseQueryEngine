<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\Snak;

/**
 * Builder for ClaimRow objects.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SnakRowBuilder {

	/**
	 * @param Snak $snak
	 * @param int $snakRole
	 * @param EntityId $subjectId
	 * @param int $statementRank
	 *
	 * @return SnakRow
	 * @throws InvalidArgumentException
	 */
	public function newSnakRow( Snak $snak, $snakRole, EntityId $subjectId, $statementRank ) {
		if ( $snak instanceof PropertyValueSnak ) {
			return $this->newValueSnakRow( $snak, $snakRole, $subjectId, $statementRank );
		}

		if ( $snak instanceof PropertySomeValueSnak || $snak instanceof PropertyNoValueSnak ) {
			return $this->newValuelessSnakRow( $snak, $snakRole, $subjectId, $statementRank );
		}

		throw new InvalidArgumentException( 'Got a snak type no supported by the SnakRowBuilder' );
	}

	private function newValueSnakRow( PropertyValueSnak $snak, $snakRole, EntityId $subjectId, $statementRank ) {
		return new ValueSnakRow(
			$snak->getDataValue(),
			$snak->getPropertyId()->getSerialization(),
			$snakRole,
			$subjectId,
			$statementRank
		);
	}

	private function newValuelessSnakRow( Snak $snak, $snakRole, EntityId $subjectId, $statementRank ) {
		$internalSnakType = $snak instanceof PropertySomeValueSnak
			? ValuelessSnakRow::TYPE_SOME_VALUE : ValuelessSnakRow::TYPE_NO_VALUE;

		return new ValuelessSnakRow(
			$internalSnakType,
			$snak->getPropertyId()->getSerialization(),
			$snakRole,
			$subjectId,
			$statementRank
		);
	}

}