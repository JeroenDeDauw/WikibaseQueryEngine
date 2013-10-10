<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\MonolingualTextValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\MonolingualTextHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MonolingualTextHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$defaultHandlers = new DataValueHandlers();
		$instances[] = $defaultHandlers->getHandler( 'monolingualtext' );

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return MonolingualTextValue[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new MonolingualTextValue( 'en', 'foo bar baz' );
		$values[] = new MonolingualTextValue( 'en', ' foo bar baz bah!' );
		$values[] = new MonolingualTextValue( 'nyan', '~=[,,_,,]:3 ~=[,,_,,]:3 ~=[,,_,,]:3 ~=[,,_,,]:3 ~=[,,_,,]:3' );
		$values[] = new MonolingualTextValue( 'fr', '' );
		$values[] = new MonolingualTextValue( 'de', '    ' );

		return $values;
	}

}
