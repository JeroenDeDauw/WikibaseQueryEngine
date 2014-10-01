<?php

namespace Wikibase\QueryEngine;

use Wikibase\DataModel\Entity\PropertyId;

/**
 * @since 0.4
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PropertyDataValueTypeLookupException extends QueryEngineException {

	private $propertyId;

	public function __construct( PropertyId $propertyId, $message = null, \Exception $previous = null ) {
		$this->propertyId = $propertyId;

		if ( $message === null ) {
			$message = 'Could not find the types of DataValues used on property : ' . $propertyId;
		}

		parent::__construct( $message, 0, $previous );
	}

	/**
	 * @return PropertyId
	 */
	public function getPropertyId() {
		return $this->propertyId;
	}

}
