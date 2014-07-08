<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use DataValues\DataValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * Unit tests for the Wikibase\QueryEngine\SQLStore\DataValueHandler implementing classes.
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
abstract class DataValueHandlerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	protected abstract function getInstances();

	/**
	 * @since 0.1
	 *
	 * @return DataValue[]
	 */
	protected abstract function getValues();

	/**
	 * @return DataValueHandler[][]
	 */
	public function instanceProvider() {
		$argLists = array();

		foreach ( $this->getInstances() as $handler ) {
			$handler->setTablePrefix( '' );
			$argLists[] = array( $handler );
		}

		return $argLists;
	}

	/**
	 * @return DataValue[][]
	 */
	public function valueProvider() {
		return $this->arrayWrap( $this->getValues() );
	}

	/**
	 * @return DataValueHandler
	 */
	protected function newInstance() {
		$instances = $this->getInstances();
		return reset( $instances );
	}

	protected function arrayWrap( array $elements ) {
		return array_map(
			function ( $element ) {
				return array( $element );
			},
			$elements
		);
	}

	/**
	 * @dataProvider instanceProvider
	 * @param DataValueHandler $dvHandler
	 */
	public function testGetTableName( DataValueHandler $dvHandler ) {
		$tableName = $dvHandler->getTableName();

		$this->assertInternalType( 'string', $tableName );
		$this->assertNotEmpty( $tableName );
		$this->assertLessThanOrEqual( 255, strlen( $tableName ) );
		$this->assertRegExp( '/^\w+$/', $tableName );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param DataValueHandler $dvHandler
	 */
	public function testConstructTableReturnType( DataValueHandler $dvHandler ) {
		$this->assertInstanceOf(
			'Doctrine\DBAL\Schema\Table',
			$dvHandler->constructTable()
		);
	}

	/**
	 * @dataProvider valueProvider
	 *
	 * @param DataValue $value
	 */
	public function testGetInsertValuesReturnType( DataValue $value ) {
		$instance = $this->newInstance();

		$insertValues = $instance->getInsertValues( $value );

		$this->assertInternalType( 'array', $insertValues );
		$this->assertNotEmpty( $insertValues );

		foreach ( $instance->getSortFieldNames() as $sortFieldName ) {
			$this->assertArrayHasKey( $sortFieldName, $insertValues );
		}
	}

	private function handlerTableHasColumn( DataValueHandler $dvHandler, $columnName ) {
		return $dvHandler->constructTable()->hasColumn( $columnName );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param DataValueHandler $dvHandler
	 */
	public function testGetEqualityFieldNameReturnValue( DataValueHandler $dvHandler ) {
		$equalityFieldName = $dvHandler->getEqualityFieldName();

		$this->assertInternalType( 'string', $equalityFieldName );
		$this->assertNotEmpty( $equalityFieldName );
		$this->assertLessThanOrEqual( 255, strlen( $equalityFieldName ) );
		$this->assertRegExp( '/^\w+$/', $equalityFieldName );

		$this->assertTrue(
			$this->handlerTableHasColumn( $dvHandler, $equalityFieldName ),
			'The equality field is present in the table'
		);
	}

	/**
	 * @dataProvider valueProvider
	 *
	 * @param DataValue $value
	 */
	public function testGetEqualityFieldValue_doesNotExceedIndexLimit( DataValue $value ) {
		$instance = $this->newInstance();

		$equalityFieldValue = $instance->getEqualityFieldValue( $value );

		$this->assertLessThanOrEqual( 255, strlen( $equalityFieldValue ) );
	}

	/**
	 * @dataProvider valueProvider
	 *
	 * @param DataValue $value
	 */
	public function testGetEqualityFieldValue_matchesEqualityField( DataValue $value ) {
		$instance = $this->newInstance();

		$insertValues = $instance->getInsertValues( $value );
		$equalityFieldName = $instance->getEqualityFieldName();
		$equalityFieldValue = $instance->getEqualityFieldValue( $value );

		$this->assertInternalType( 'array', $insertValues );
		$this->assertArrayHasKey( $equalityFieldName, $insertValues );
		$this->assertEquals( $insertValues[$equalityFieldName], $equalityFieldValue );
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param DataValueHandler $dvHandler
	 */
	public function testGetSortFieldNameReturnValue( DataValueHandler $dvHandler ) {
		$sortFieldNames = $dvHandler->getSortFieldNames();

		$this->assertInternalType( 'array', $sortFieldNames );
		$this->assertNotEmpty( $sortFieldNames );

		foreach ( $sortFieldNames as $sortFieldName ) {
			$this->assertInternalType( 'string', $sortFieldName );

			$this->assertTrue(
				$this->handlerTableHasColumn( $dvHandler, $sortFieldName ),
				'The sort field is present in the table'
			);
		}
	}

}
