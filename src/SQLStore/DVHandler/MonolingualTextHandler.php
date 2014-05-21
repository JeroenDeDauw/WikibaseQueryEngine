<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\MonolingualTextValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\StringHasher;

/**
 * Represents the mapping between DataValues\MonolingualTextValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MonolingualTextHandler extends DataValueHandler {

	/**
	 * @var StringHasher
	 */
	private $stringHasher;

	/**
	 * @see DataValueHandler::getBaseTableName
	 */
	protected function getBaseTableName() {
		return 'mono_text';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value_text', Type::TEXT );
		$table->addColumn( 'value_language', Type::STRING, array( 'length' => 20 ) );
		$table->addColumn( 'value_json', Type::TEXT );
		$table->addColumn( 'hash', Type::STRING, array( 'length' => StringHasher::LENGTH ) );

		// TODO: check what indexes should be added
	}

	/**
	 * @see DataValueHandler::getValueFieldName
	 */
	public function getValueFieldName() {
		return 'value_json';
	}

	/**
	 * @see DataValueHandler::getEqualityFieldName
	 */
	public function getEqualityFieldName() {
		return 'hash';
	}

	/**
	 * @see DataValueHandler::getSortFieldName
	 */
	public function getSortFieldName() {
		return 'hash';
	}

	/**
	 * @see DataValueHandler::getLabelFieldName
	 */
	public function getLabelFieldName() {
		return 'value_text';
	}

	/**
	 * @see DataValueHandler::newDataValueFromValueField
	 *
	 * @param string $valueFieldValue
	 *
	 * @return DataValue
	 */
	public function newDataValueFromValueField( $valueFieldValue ) {
		return MonolingualTextValue::newFromArray( json_decode( $valueFieldValue, true ) );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @param DataValue $value
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof MonolingualTextValue ) ) {
			throw new InvalidArgumentException( 'Value is not a MonolingualTextValue' );
		}

		$values = array(
			'value_text' => $value->getText(),
			'value_language' => $value->getLanguageCode(),

			'value_json' => json_encode( $value->getArrayValue() ),
			'hash' => $this->getEqualityFieldValue( $value )
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getEqualityFieldValue
	 *
	 * @param DataValue $value
	 *
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof MonolingualTextValue ) ) {
			throw new InvalidArgumentException( 'Value is not a MonolingualTextValue' );
		}

		return $this->hash( $value->getText() . $value->getLanguageCode() );
	}

	private function hash( $string ) {
		if ( $this->stringHasher === null ) {
			$this->stringHasher = new StringHasher();
		}

		return $this->stringHasher->hash( $string );
	}

}
