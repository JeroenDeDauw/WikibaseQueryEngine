<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use Wikibase\DataModel\Entity\EntityId;

/**
 * Use case for removing snaks from the store.
 *
 * TODO: this can be made more efficient by providing the list of snaks
 * the entity has, so deletes can be run just against the relevant
 * data value type specific tables.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SnakRemover {

	/**
	 * @var SnakStore[]
	 */
	private $snakStores;

	/**
	 * @param SnakStore[] $snakStores
	 */
	public function __construct( array $snakStores ) {
		$this->snakStores = $snakStores;
	}

	/**
	 * @param EntityId $subjectId
	 *
	 * TODO: exception
	 */
	public function removeSnaksOfSubject( EntityId $subjectId ) {
		foreach ( $this->snakStores as $snakStore ) {
			$snakStore->removeSnaksOfSubject( $subjectId );
		}
	}

}
