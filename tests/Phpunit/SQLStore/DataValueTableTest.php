<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DataValueTable;

/**
 * Unit tests for the Wikibase\QueryEngine\SQLStore\DataValueHandler implementing classes.
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DataValueTableTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @since 0.1
	 *
	 * @return DataValueTable[][]
	 */
	public function instanceProvider() {
		$defaultHandlers = new DataValueHandlers();

		$argLists = array();

		foreach ( $defaultHandlers->getHandlers() as $handler ) {
			$argLists[] = array( $handler->getDataValueTable() );
		}

		return $argLists;
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param DataValueTable $dvTable
	 */
	public function testGetValueFieldNameReturnValue( DataValueTable $dvTable ) {
		$valueFieldName = $dvTable->getValueFieldName();

		$this->assertInternalType( 'string', $valueFieldName );

		$this->assertTrue(
			$dvTable->getTableDefinition()->hasFieldWithName( $valueFieldName ),
			'The value field is present in the table'
		);
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param DataValueTable $dvTable
	 */
	public function testGetSortFieldNameReturnValue( DataValueTable $dvTable ) {
		$sortFieldName = $dvTable->getSortFieldName();

		$this->assertInternalType( 'string', $sortFieldName );

		$this->assertTrue(
			$dvTable->getTableDefinition()->hasFieldWithName( $sortFieldName ),
			'The sort field is present in the table'
		);
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param DataValueTable $dvTable
	 */
	public function testGetLabelFieldNameReturnValue( DataValueTable $dvTable ) {
		$labelFieldName = $dvTable->getLabelFieldName();

		$this->assertTrue(
			$labelFieldName === null || is_string( $labelFieldName ),
			'The label field name needs to be either string or null'
		);

		if ( is_string( $labelFieldName ) ) {
			$this->assertTrue(
				$dvTable->getTableDefinition()->hasFieldWithName( $labelFieldName ),
				'The label field is present in the table'
			);
		}
	}

}
