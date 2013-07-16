<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use RuntimeException;
use Wikibase\Snak;

/**
 * Use case for inserting snaks into the store.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
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
	 * @param int $internalClaimId
	 * @param int $internalSubjectId
	 */
	public function insertSnak( Snak $snak, $snakRole, $internalClaimId, $internalSubjectId ) {
		$snakRow = $this->snakRowBuilder->newSnakRow( $snak, $snakRole, $internalClaimId, $internalSubjectId );
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
