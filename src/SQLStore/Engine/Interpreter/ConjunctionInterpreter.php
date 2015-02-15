<?php

namespace Wikibase\QueryEngine\SQLStore\Engine\Interpreter;

use Ask\Language\Description\Conjunction;
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
	 * @var DescriptionInterpreter
	 */
	private $subInterpreter;

	public function __construct( DescriptionInterpreter $subInterpreter ) {
		$this->subInterpreter = $subInterpreter;
	}

	/**
	 * @param Description $description
	 *
	 * @return boolean
	 */
	public function canInterpretDescription( Description $description ) {
		return $description instanceof Conjunction;
	}

	/**
	 * @param Description $description
	 *
	 * @return SqlQueryPart
	 * @throws InvalidArgumentException
	 */
	public function interpretDescription( Description $description ) {
		if ( !( $description instanceof Conjunction ) ) {
			throw new InvalidArgumentException( 'Can only interpret conjunctions' );
		}

		return new SqlQueryPart(); // TODO
	}

}