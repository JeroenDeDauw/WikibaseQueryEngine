<?php

namespace Wikibase\QueryEngine;

use InvalidArgumentException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StringHasher  {

	private $PLAIN_LENGTH = 30;
	private $SHA_LENGTH = 20; // max 40 due to sha1 usage
	private $MAX_LENGTH;

	public function __construct() {
		$this->MAX_LENGTH = $this->PLAIN_LENGTH + $this->SHA_LENGTH;
	}

	/**
	 * Returns a version of the string with maximum length 50.
	 * The first 30 characters of the string are kept as-is in all cases.
	 * If the string reaches the max length, the end of the space is
	 * used for a hash that ensures uniqueness.
	 *
	 * @param string $string
	 *
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function hash( $string ) {
		if ( !is_string( $string ) ) {
			throw new InvalidArgumentException( '$string should be a string' );
		}

		if ( strlen( $string ) >= $this->MAX_LENGTH ) {
			return substr( $string, 0, $this->PLAIN_LENGTH )
				. substr( sha1( substr( $string, $this->PLAIN_LENGTH ) ), 0, $this->SHA_LENGTH );
		}

		return $string;
	}

}
