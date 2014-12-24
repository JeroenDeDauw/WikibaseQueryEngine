<?php

namespace Wikibase\QueryEngine;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface DescriptionMatchFinder {

	/**
	 * @since 0.1
	 *
	 * @param Description $description
	 * @param QueryOptions $options
	 *
	 * @return EntityId[]
	 */
	public function getMatchingEntities( Description $description, QueryOptions $options );

}
