<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use RuntimeException;
use Wikibase\Snak;

/**
 * Use case for removing snaks from the store.
 *
 * TODO: this can be made more efficient by providing the list of snaks
 * the entity has, so deletes can be run just against the relevant
 * data value type specific tables.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SnakRemover {

	/**
	 * @var SnakStore[]
	 */
	protected $snakStores;

	/**
	 * @param SnakStore[] $snakStores
	 */
	public function __construct( array $snakStores ) {
		$this->snakStores = $snakStores;
	}

	/**
	 * @since 0.1
	 *
	 * @param int $internalSubjectId
	 */
	public function removeSnaksOfSubject( $internalSubjectId ) {
		foreach ( $this->snakStores as $snakStore ) {
			$snakStore->removeSnaksOfSubject( $internalSubjectId );
		}
	}

}
