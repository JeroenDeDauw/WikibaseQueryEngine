<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\IriValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\IriHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IriHandlerTest extends DataValueHandlerTest {

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
		$instances[] = $defaultHandlers->getHandler( 'iri' );

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @since 0.1
	 *
	 * @return IriValue[]
	 */
	protected function getValues() {
		$values = array();

		$values[] = new IriValue( 'ohi', 'foo', 'bar', 'baz' );
		$values[] = new IriValue( 'http', '//www.wikidata.org/w/index.php', 'title=Special:Version', 'sv-credits-datavalues' );
		$values[] = new IriValue( 'ohi', 'foo', '', 'baz' );
		$values[] = new IriValue( 'ohi', 'foo', 'bar', '' );
		$values[] = new IriValue( 'ohi', 'foo', '', '' );
		$values[] = new IriValue( 'ohi', 'foo' );

		return $values;
	}

}
