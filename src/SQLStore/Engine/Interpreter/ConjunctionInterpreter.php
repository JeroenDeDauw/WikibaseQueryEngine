<?php

namespace Wikibase\QueryEngine\SQLStore\Engine\Interpreter;

use Ask\Language\Description\Description;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionInterpreter;
use Wikibase\QueryEngine\SQLStore\Engine\SqlQueryPart;

/**
 * @private
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ConjunctionInterpreter implements DescriptionInterpreter {

	/**
	 * @param Description $description
	 *
	 * @return boolean
	 */
	public function canInterpretDescription( Description $description ) {
		return false; // TODO
	}

	/**
	 * @param Description $description
	 *
	 * @return SqlQueryPart
	 * @throws InvalidArgumentException
	 */
	public function interpretDescription( Description $description ) {
		throw new InvalidArgumentException( 'Can only interpret conjunctions' );
	}

}