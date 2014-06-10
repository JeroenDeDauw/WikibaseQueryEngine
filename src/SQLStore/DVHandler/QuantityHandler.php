<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\QuantityValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\StringHasher;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class QuantityHandler extends DataValueHandler {

	/**
	 * @var StringHasher|null
	 */
	private $stringHasher = null;

	/**
	 * @see DataValueHandler::getBaseTableName
	 */
	protected function getBaseTableName() {
		return 'quantity';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value', Type::STRING, array( 'length' => StringHasher::LENGTH ) );
		$table->addColumn( 'value_actual', Type::DECIMAL );
		$table->addColumn( 'value_lower_bound', Type::DECIMAL );
		$table->addColumn( 'value_upper_bound', Type::DECIMAL );

		// XXX: Does the equality field get an index automatically?
		$table->addIndex( array( 'value_lower_bound' ) );
		$table->addIndex( array( 'value_upper_bound' ) );
	}

	/**
	 * @see DataValueHandler::getValueFieldName
	 */
	public function getValueFieldName() {
		return 'value_actual';
	}

	/**
	 * @see DataValueHandler::getEqualityFieldName
	 */
	public function getEqualityFieldName() {
		return 'value';
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof QuantityValue ) ) {
			throw new InvalidArgumentException( 'Value is not a QuantityValue' );
		}

		$values = array(
			'value_actual' => $value->getAmount()->getValueFloat(),
			'value_lower_bound' => $value->getLowerBound()->getValueFloat(),
			'value_upper_bound' => $value->getUpperBound()->getValueFloat(),

			'value' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getEqualityFieldValue
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return float
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof QuantityValue ) ) {
			throw new InvalidArgumentException( 'Value is not a QuantityValue.' );
		}

		$string = strval( $value->getAmount()->getValueFloat() );
		if ( $value->getUnit() !== '1' ) {
			$string .= ' ' . $value->getUnit();
		}
		return $this->hash( $string );
	}

	private function hash( $string ) {
		if ( $this->stringHasher === null ) {
			$this->stringHasher = new StringHasher();
		}

		return $this->stringHasher->hash( $string );
	}

}
