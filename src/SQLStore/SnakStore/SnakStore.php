<?php

namespace Wikibase\QueryEngine\SQLStore\SnakStore;

/**
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
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
	 */
	public abstract function storeSnakRow( SnakRow $snakRow );

	/**
	 * @since 0.1
	 *
	 * @param int $internalSubjectId
	 */
	public abstract function removeSnaksOfSubject( $internalSubjectId );

}
