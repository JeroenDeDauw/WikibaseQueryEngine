<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use Wikibase\DataModel\Entity\EntityId;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SnakStore {

	/**
	 * @since 0.1
	 *
	 * @param SnakRow $snakRow
	 *
	 * @return boolean
	 */
	public abstract function canStore( SnakRow $snakRow );

	/**
	 * @since 0.1
	 *
	 * @param SnakRow $snakRow
	 *
	 * TODO: exception
	 */
	public abstract function storeSnakRow( SnakRow $snakRow );

	/**
	 * @since 0.1
	 *
	 * @param EntityId $subjectId
	 *
	 * TODO: exception
	 */
	public abstract function removeSnaksOfSubject( EntityId $subjectId );

}
