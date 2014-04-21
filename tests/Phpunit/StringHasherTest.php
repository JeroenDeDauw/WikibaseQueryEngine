<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\StringHasher;

/**
 * @covers Wikibase\QueryEngine\StringHasher
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class StringHasherTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @see StringHasher::__construct
	 */
	private $MAX_LENGTH = 50;

	/**
	 * @var StringHasher
	 */
	private $hasher;

	public function setUp() {
		$this->hasher = new StringHasher();
	}

	private function assertStringToHash( $string, $expectedHash ) {
		$this->assertEquals( $expectedHash, $this->hasher->hash( $string ) );
	}

	public function testGivenShortString_isReturnedAsIs() {
		$this->assertStringToHash( '', '' );
		$this->assertStringToHash( 'a', 'a' );
		$this->assertStringToHash( 'ab cd ef gh', 'ab cd ef gh' );
	}

	public function testGivenStringExceedingPlainLength_isNotHashed() {
		$maxMinusOneString = str_pad( '', $this->MAX_LENGTH - 1, '0123456789' );

		$this->assertStringToHash( $maxMinusOneString, $maxMinusOneString );
	}

	public function testGivenNonString_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		$this->hasher->hash( null );
	}

	public function testGivenStringExceedingMaxLength_maxLengthStringIsReturned() {
		$maxPlusOneString = str_pad( '', $this->MAX_LENGTH + 1, '0123456789' );
		$hash = $this->hasher->hash( $maxPlusOneString );

		$this->assertEquals( $this->MAX_LENGTH, strlen( $hash ) );
	}

	public function testGivenStringThatCollidesWithAHash_isNotReturnedAsIs() {
		$maxPlusOneString = str_pad( '', $this->MAX_LENGTH + 1, '0123456789' );
		$collidingString = $this->hasher->hash( $maxPlusOneString );
		$hash = $this->hasher->hash( $collidingString );

		$this->assertNotEquals( $collidingString, $hash );
	}

	public function testGivenTwoStringsThatExceedMaxLength_hashIsNotTheSame() {
		$maxString = str_pad( '', $this->MAX_LENGTH, '0123456789' );
		$hash0 = $this->hasher->hash( $maxString . 'A' );
		$hash1 = $this->hasher->hash( $maxString . 'B' );

		$this->assertNotEquals( $hash0, $hash1 );
	}

	public function testGivenStringWithoutBaseIsTheSame_hashWithoutBaseIsTheSame() {
		$maxString = str_pad( '', $this->MAX_LENGTH, '0123456789' );
		$hash0 = $this->hasher->hash( 'a' . $maxString );
		$hash1 = $this->hasher->hash( 'b' . $maxString );

		$this->assertEquals( substr( $hash0, 1 ), substr( $hash1, 1 ) );
	}

}
