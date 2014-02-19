<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use RuntimeException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Snak\Snak;

/**
 * Use case for inserting snaks into the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SnakInserter {

	/**
	 * @var SnakStore[]
	 */
	protected $snakStores;

	protected $snakRowBuilder;

	/**
	 * @param SnakStore[] $snakStores
	 * @param SnakRowBuilder $snakRowBuilder
	 */
	public function __construct( array $snakStores, SnakRowBuilder $snakRowBuilder ) {
		$this->snakStores = $snakStores;
		$this->snakRowBuilder = $snakRowBuilder;
	}

	/**
	 * @since 0.1
	 *
	 * @param Snak $snak
	 * @param int $snakRole
	 * @param EntityId $subjectId
	 */
	public function insertSnak( Snak $snak, $snakRole, EntityId $subjectId ) {
		$snakRow = $this->snakRowBuilder->newSnakRow( $snak, $snakRole, $subjectId );
		$this->insertSnakRow( $snakRow );
	}

	protected function insertSnakRow( SnakRow $snakRow ) {
		foreach ( $this->snakStores as $snakStore ) {
			if ( $snakStore->canStore( $snakRow ) ) {
				$snakStore->storeSnakRow( $snakRow );
				return;
			}
		}

		throw new RuntimeException( 'Cannot store the snak as there is no SnakStore that can handle it' );
	}

}
