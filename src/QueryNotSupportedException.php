<?php

namespace Wikibase\QueryEngine;

use Ask\Language\Description\Description;
use Exception;

/**
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseQueryStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class QueryNotSupportedException extends QueryEngineException {

	private $queryDescription;

	public function __construct( Description $queryDescription, $message = '', Exception $previous = null ) {
		$this->queryDescription = $queryDescription;

		parent::__construct( $message, 0, $previous );
	}

	/**
	 * @return Description
	 */
	public function getQueryDescription() {
		return $this->queryDescription;
	}

}
