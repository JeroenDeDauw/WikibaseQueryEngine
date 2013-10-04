<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\PropertyNoValueSnak;
use Wikibase\PropertySomeValueSnak;
use Wikibase\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\InternalEntityIdFinder;
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

	protected $idFinder;

	public function __construct( InternalEntityIdFinder $idFinder ) {
		$this->idFinder = $idFinder;
	}

	/**
	 * @since 0.1
	 *
	 * @param Snak $snak
	 * @param int $snakRole
	 * @param int $internalSubjectId
	 *
	 * @return SnakRow
	 * @throws InvalidArgumentException
	 */
	public function newSnakRow( Snak $snak, $snakRole, $internalSubjectId ) {
		if ( $snak instanceof PropertyValueSnak ) {
			return $this->newValueSnakRow( $snak, $snakRole, $internalSubjectId );
		}

		if ( $snak instanceof PropertySomeValueSnak || $snak instanceof PropertyNoValueSnak ) {
			return $this->newValuelessSnakRow( $snak, $snakRole, $internalSubjectId );
		}

		throw new InvalidArgumentException( 'Got a snak type no supported by the SnakRowBuilder' );
	}

	protected function newValueSnakRow( PropertyValueSnak $snak, $snakRole, $internalSubjectId ) {
		return new ValueSnakRow(
			$snak->getDataValue(),
			$this->getInternalIdFor( $snak->getPropertyId() ),
			$snakRole,
			$internalSubjectId
		);
	}

	protected function newValuelessSnakRow( Snak $snak, $snakRole, $internalSubjectId ) {
		$internalSnakType = $snak instanceof PropertySomeValueSnak
			? ValuelessSnakRow::TYPE_SOME_VALUE : ValuelessSnakRow::TYPE_NO_VALUE;

		return new ValuelessSnakRow(
			$internalSnakType,
			$this->getInternalIdFor( $snak->getPropertyId() ),
			$snakRole,
			$internalSubjectId
		);
	}

	protected function getInternalIdFor( EntityId $entityId ) {
		return $this->idFinder->getInternalIdForEntity( $entityId );
	}

}