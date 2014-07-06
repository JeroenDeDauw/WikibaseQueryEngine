<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\UnknownValue;
use InvalidArgumentException;
use RuntimeException;
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

	private function assertIsValidDatabaseIdentifier( $identifier ) {
		$this->assertInternalType( 'string', $identifier );
		$this->assertNotEmpty( $identifier );
		$this->assertLessThanOrEqual( 255, strlen( $identifier ) );
		$this->assertRegExp( '/^\w+$/', $identifier );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetTableName( DataValueHandler $dvHandler ) {
		$tableName = $dvHandler->getTableName();

		$this->assertIsValidDatabaseIdentifier( $tableName );
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testGetTableName_tablePrefixMustBeSet() {
		$instance = $this->newInstance();
		$instance->getTableName();
	}

	/**
	 * @dataProvider instanceProvider
	 * @expectedException RuntimeException
	 */
	public function testSetTablePrefix_canNotBeChanged( DataValueHandler $dvHandler ) {
		$dvHandler->setTablePrefix( '' );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testConstructTableReturnType( DataValueHandler $dvHandler ) {
		$this->assertInstanceOf(
			'Doctrine\DBAL\Schema\Table',
			$dvHandler->constructTable()
		);
	}

	/**
	 * @dataProvider instanceProvider
	 * @expectedException InvalidArgumentException
	 */
	public function testGetInsertValues_failsOnUnknownTypes( DataValueHandler $dvHandler ) {
		$dvHandler->getInsertValues( new UnknownValue( null ) );
	}

	/**
	 * @dataProvider valueProvider
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
	 */
	public function testGetEqualityFieldNameReturnValue( DataValueHandler $dvHandler ) {
		$equalityFieldName = $dvHandler->getEqualityFieldName();

		$this->assertIsValidDatabaseIdentifier( $equalityFieldName );
		$this->assertTrue(
			$this->handlerTableHasColumn( $dvHandler, $equalityFieldName ),
			'The equality field is present in the table'
		);
	}

	/**
	 * @dataProvider valueProvider
	 */
	public function testGetEqualityFieldValue_doesNotExceedIndexLimit( DataValue $value ) {
		$instance = $this->newInstance();

		$equalityFieldValue = $instance->getEqualityFieldValue( $value );

		$this->assertLessThanOrEqual( 255, strlen( $equalityFieldValue ) );
	}

	/**
	 * @dataProvider valueProvider
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

	private function getQueryBuilderMock() {
		$builder = $this->getMockBuilder( '\Doctrine\DBAL\Query\QueryBuilder' )
			->disableOriginalConstructor()
			->getMock();

		return $builder;
	}

	/**
	 * @dataProvider instanceProvider
	 * @param DataValueHandler $dvHandler
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddMatchConditions_invalidDataValue( DataValueHandler $dvHandler ) {
		$value = new UnknownValue( null );

		$description = new ValueDescription( $value );
		$dvHandler->addMatchConditions( $this->getQueryBuilderMock(), $description );
	}

	/**
	 * @dataProvider valueProvider
	 * @param DataValue $value
	 * @expectedException \Wikibase\QueryEngine\QueryNotSupportedException
	 */
	public function testAddMatchConditions_notSupportedOperator( DataValue $value ) {
		$dvHandler = $this->newInstance();
		$builder = $this->getQueryBuilderMock();

		$description = new ValueDescription( $value, ValueDescription::COMP_GREATER );
		$dvHandler->addMatchConditions( $builder, $description );
	}

	/**
	 * @dataProvider valueProvider
	 * @param DataValue $value
	 */
	public function testAddMatchConditions_addsAtLeastOneWhereCondition( DataValue $value ) {
		$dvHandler = $this->newInstance();
		$dvHandler->setTablePrefix( '' );
		$builder = $this->getQueryBuilderMock();
		$builder->expects( $this->atLeastOnce() )
			->method( 'andWhere' );

		$description = new ValueDescription( $value, ValueDescription::COMP_EQUAL );
		$dvHandler->addMatchConditions( $builder, $description );
	}

}
