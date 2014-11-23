<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\QueryEngine\QueryEngineException;

/**
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
	 * @throws QueryEngineException
	 */
	public abstract function storeSnakRow( SnakRow $snakRow );

	/**
	 * @since 0.1
	 *
	 * @param EntityId $subjectId
	 *
	 * @throws QueryEngineException
	 */
	public abstract function removeSnaksOfSubject( EntityId $subjectId );

}
