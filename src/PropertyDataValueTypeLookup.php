<?php

namespace Wikibase\QueryEngine;

use Wikibase\DataModel\Entity\PropertyId;

/**
 * Interface for objects that can find the type of the DataValue that
 * a property (which is specified by its ID) has values of.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface PropertyDataValueTypeLookup {

	/**
	 * @param PropertyId $propertyId
	 *
	 * @return string The type of the data values has values of
	 *
	 * @throws PropertyDataValueTypeLookupException
	 */
	public function getDataValueTypeForProperty( PropertyId $propertyId );

}
