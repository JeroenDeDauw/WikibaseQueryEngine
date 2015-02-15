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
class DispatchingInterpreter implements DescriptionInterpreter {

	/**
	 * @var DescriptionInterpreter[]
	 */
	private $interpreters = [];

	/**
	 * @param Description $description
	 *
	 * @return boolean
	 */
	public function canInterpretDescription( Description $description ) {
		foreach ( $this->interpreters as $interpreter ) {
			if ( $interpreter->canInterpretDescription( $description ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param Description $description
	 *
	 * @return SqlQueryPart
	 * @throws InvalidArgumentException
	 */
	public function interpretDescription( Description $description ) {
		foreach ( $this->interpreters as $interpreter ) {
			if ( $interpreter->canInterpretDescription( $description ) ) {
				return $interpreter->interpretDescription( $description );
			}
		}

		throw new InvalidArgumentException( 'There is no interpreter that can handle descriptions of this type' );
	}

	public function addInterpreter( DescriptionInterpreter $interpreter ) {
		$this->interpreters[] = $interpreter;
	}

}
