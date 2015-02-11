<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\MonolingualTextValue;
use Wikibase\QueryEngine\SQLStore\DVHandler\MonolingualTextHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\MonolingualTextHandler
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class MonolingualTextHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @return MonolingualTextHandler[]
	 */
	protected function getInstances() {
		$instances = [];

		$instances[] = new MonolingualTextHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @return MonolingualTextValue[]
	 */
	protected function getValues() {
		$values = [];

		$values[] = new MonolingualTextValue( 'en', 'foo bar baz' );
		$values[] = new MonolingualTextValue( 'en', ' foo bar baz bah!' );
		$values[] = new MonolingualTextValue( 'nyan', '~=[,,_,,]:3 ~=[,,_,,]:3 ~=[,,_,,]:3 ~=[,,_,,]:3 ~=[,,_,,]:3' );
		$values[] = new MonolingualTextValue( 'fr', '' );
		$values[] = new MonolingualTextValue( 'de', '    ' );

		return $values;
	}

	public function testGetEqualityFieldValue_shortStrings() {
		$handler = $this->newInstance();

		$value = new MonolingualTextValue( 'en', 'short' );
		$hash = $handler->getEqualityFieldValue( $value );

		$this->assertEquals( $hash, 'short|en', 'Should not encode short strings' );
	}

	public function testGetEqualityFieldValue_longStrings() {
		$handler = $this->newInstance();

		$value = new MonolingualTextValue( 'en', str_repeat( 'abcd', 256 ) );
		$hash = $handler->getEqualityFieldValue( $value );

		$this->assertLessThanOrEqual( 255, strlen( $hash ), 'Can not exceed index limit' );
	}

	public function hashCollisionProvider() {
		return array(
			array( 'a', 'bc', 'ab', 'c' ),
			array( 'bc', 'a', 'c', 'ab' ),
			array( '|b', 'a', 'b', 'a|' ),
			array( 'b', 'a\\|\\\\', '\\|b', 'a\\\\' ),
			array( 'b)', 'a (', ' (b)', 'a' ),
		);
	}

	/**
	 * @dataProvider hashCollisionProvider
	 */
	public function testGetEqualityFieldValue_hashCollisions( $languageCode1, $text1, $languageCode2, $text2 ) {
		$handler = $this->newInstance();

		$value1 = new MonolingualTextValue( $languageCode1, $text1 );
		$value2 = new MonolingualTextValue( $languageCode2, $text2 );
		$hash1 = $handler->getEqualityFieldValue( $value1 );
		$hash2 = $handler->getEqualityFieldValue( $value2 );

		$this->assertNotEquals( $hash1, $hash2 );
	}

}
