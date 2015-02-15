<?php

namespace Wikibase\QueryEngine\Tests\Fixtures;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Description;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionInterpreter;
use Wikibase\QueryEngine\SQLStore\Engine\SqlQueryPart;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FakeAnyValueInterpreter implements DescriptionInterpreter {

	/**
	 * @param Description $description
	 *
	 * @return boolean
	 */
	public function canInterpretDescription( Description $description ) {
		return $description instanceof AnyValue;
	}

	/**
	 * @param Description $description
	 *
	 * @return SqlQueryPart
	 * @throws InvalidArgumentException
	 */
	public function interpretDescription( Description $description ) {
		return new SqlQueryPart();
	}

}
