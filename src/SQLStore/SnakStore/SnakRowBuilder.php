<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\PropertyNoValueSnak;
use Wikibase\PropertySomeValueSnak;
use Wikibase\PropertyValueSnak;
use Wikibase\Snak;

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
	 * @since 0.1
	 *
	 * @param Snak $snak
	 * @param int $snakRole
	 * @param EntityId $subjectId
	 *
	 * @return SnakRow
	 * @throws InvalidArgumentException
	 */
	public function newSnakRow( Snak $snak, $snakRole, EntityId $subjectId ) {
		if ( $snak instanceof PropertyValueSnak ) {
			return $this->newValueSnakRow( $snak, $snakRole, $subjectId );
		}

		if ( $snak instanceof PropertySomeValueSnak || $snak instanceof PropertyNoValueSnak ) {
			return $this->newValuelessSnakRow( $snak, $snakRole, $subjectId );
		}

		throw new InvalidArgumentException( 'Got a snak type no supported by the SnakRowBuilder' );
	}

	protected function newValueSnakRow( PropertyValueSnak $snak, $snakRole, EntityId $subjectId ) {
		return new ValueSnakRow(
			$snak->getDataValue(),
			$snak->getPropertyId()->getSerialization(),
			$snakRole,
			$subjectId->getSerialization()
		);
	}

	protected function newValuelessSnakRow( Snak $snak, $snakRole, EntityId $subjectId ) {
		$internalSnakType = $snak instanceof PropertySomeValueSnak
			? ValuelessSnakRow::TYPE_SOME_VALUE : ValuelessSnakRow::TYPE_NO_VALUE;

		return new ValuelessSnakRow(
			$internalSnakType,
			$snak->getPropertyId()->getSerialization(),
			$snakRole,
			$subjectId->getSerialization()
		);
	}

}