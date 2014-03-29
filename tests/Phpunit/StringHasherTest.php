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
 */
class StringHasherTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var StringHasher
	 */
	private $hasher;

	public function setUp() {
		$this->hasher = new StringHasher();
	}

	public function testGivenShortString_isReturnedAsIs() {
		$this->assertStringToHash( '', '' );
		$this->assertStringToHash( 'a', 'a' );
		$this->assertStringToHash( 'ab cd ef gh', 'ab cd ef gh' );

		$fiftyChars = '01234567890123456789012345678901234567890123456789';
		$this->assertStringToHash( $fiftyChars, $fiftyChars );
	}

	private function assertStringToHash( $string, $expectedHash ) {
		$this->assertEquals( $expectedHash, $this->hasher->hash( $string ) );
	}

	public function testGivenNonString_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		$this->hasher->hash( null );
	}

	public function testGivenStringExceedingMaxLength_maxLengthStringIsReturned() {
		$hash = $this->hasher->hash( '012345678901234567890123456789012345678901234567890123456789' );
		$this->assertEquals( 50, strlen( $hash ) );
	}

	public function testGivenTwoStringsThatExceedMaxLength_hashIsNotTheSame() {
		$hash0 = $this->hasher->hash( '012345678901234567890123456789012345678901234567890123456789A' );
		$hash1 = $this->hasher->hash( '012345678901234567890123456789012345678901234567890123456789B' );

		$this->assertNotEquals( $hash0, $hash1 );
	}

}
