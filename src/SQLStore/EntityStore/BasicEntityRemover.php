<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BasicEntityRemover implements EntityRemovalStrategy {

	private $snakRemover;

	public function __construct( SnakRemover $snakRemover ) {
		$this->snakRemover = $snakRemover;
	}

	public function removeEntity( EntityDocument $entity ) {
		$this->snakRemover->removeSnaksOfSubject( $entity->getId() );
	}

	/**
	 * @param EntityDocument $entity
	 *
	 * @return boolean
	 */
	public function canRemove( EntityDocument $entity ) {
		return true;
	}

}
