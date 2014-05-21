<?php

namespace Wikibase\QueryEngine;

use InvalidArgumentException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class StringHasher {

	/**
	 * A SHA1 hash is 20 binary bytes (or 40 hexadecimal characters). With BASE64 encoding this
	 * becomes ceil( 20 * 8 / 6 ) = 27 ASCII characters. Since BASE64 must always be a multiple
	 * of 4 it adds a meaningless "=" character. This adds no benefit to the hash (it would be
	 * the same in all hashes) so we strip it.
	 *
	 * This leaves 63 - 27 = 36 raw (plain) characters from the original string.
	 *
	 * The 63 was an arbitrary decision (maximum 6 bit number). Could also be 65 or 70.
	 */
	const LENGTH = 63;

	private $rawLength;
	private $hashedLength = 27;

	function __construct() {
		$this->rawLength = self::LENGTH - $this->hashedLength;
	}

	/**
	 * Returns a version of the string with maximum length 63.
	 * The first 36 characters of the string are kept as-is in all cases.
	 * If the string reaches the maximum length, the end of the space is
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

		if ( strlen( $string ) >= self::LENGTH ) {
			return substr( $string, 0, $this->rawLength )
				. substr( base64_encode( sha1( substr( $string, $this->rawLength ), true ) ), 0, $this->hashedLength );
		}

		return $string;
	}

}
