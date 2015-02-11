<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

use Ask\Language\Description\Description;

/**
 * Interprets an (Ask language) Description into a SqlQueryPart.
 *
 * @private
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface DescriptionInterpreter {

	/**
	 * @param Description $description
	 *
	 * @return boolean
	 */
	public function canInterpretDescription( Description $description );

	/**
	 * @param Description $description
	 *
	 * @return SqlQueryPart
	 * @throws \InvalidArgumentException
	 */
	public function interpretDescription( Description $description );

}