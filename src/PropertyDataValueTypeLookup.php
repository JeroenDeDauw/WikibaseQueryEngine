<?php

namespace Wikibase\QueryEngine;

use DataValues\DataValue;

/**
 * Interface for objects that can find the type of the DataValue that
 * a property (which is specified by its ID) has values of.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface PropertyDataValueTypeLookup {

	/**
	 * @param DataValue $propertyId
	 *
	 * @return string The type of the data values has values of
	 *
	 * TODO: provide exception type and document
	 */
	public function getDataValueTypeForProperty( DataValue $propertyId );

}