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
	 * @since 0.1
	 *
	 * @return DataValueHandler[][]
	 */
	public function instanceProvider() {
		return $this->arrayWrap( $this->getInstances() );
	}

	/**
	 * @since 0.1
	 *
	 * @return DataValue[][]
	 */
	public function valueProvider() {
		return $this->arrayWrap( $this->getValues() );
	}

	/**
	 * @since 0.1
	 *
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
	 *
	 * @param DataValueHandler $dvHandler
	 */
	public function testGetDataValueTableReturnType( DataValueHandler $dvHandler ) {
		$this->assertInstanceOf( 'Wikibase\QueryEngine\SQLStore\DataValueTable', $dvHandler->getDataValueTable() );
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

		$this->assertArrayHasKey( $instance->getDataValueTable()->getValueFieldName(), $insertValues );
		$this->assertArrayHasKey( $instance->getDataValueTable()->getSortFieldName(), $insertValues );

		if ( $instance->getDataValueTable()->getLabelFieldName() !== null ) {
			$this->assertArrayHasKey( $instance->getDataValueTable()->getLabelFieldName(), $insertValues );
		}
	}

	/**
	 * @dataProvider valueProvider
	 *
	 * @param DataValue $value
	 */
	public function testNewDataValueFromValueFieldValue( DataValue $value ) {
		$instance = $this->newInstance();

		$fieldValues = $instance->getInsertValues( $value );
		$valueFieldValue = $fieldValues[$instance->getDataValueTable()->getValueFieldName()];

		$newValue = $instance->newDataValueFromValueField( $valueFieldValue );

		$this->assertTrue(
			$value->equals( $newValue ),
			'Newly constructed DataValue equals the old one'
		);
	}

}
