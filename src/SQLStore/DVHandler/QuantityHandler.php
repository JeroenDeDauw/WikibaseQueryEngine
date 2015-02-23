<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\DecimalValue;
use DataValues\QuantityValue;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\WhereConditions;

/**
 * @since 0.3
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class QuantityHandler extends DataValueHandler {

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
		$table->addColumn( 'value_unit', Type::STRING, array( 'length' => 255, 'notnull' => false ) );
		$table->addColumn( 'value_actual', Type::FLOAT );
		$table->addColumn( 'value_lower_bound', Type::FLOAT );
		$table->addColumn( 'value_upper_bound', Type::FLOAT );
		$table->addColumn( 'hash', Type::STRING, array( 'length' => 32 ) );

		$table->addIndex( array( 'value_actual' ) );
		// TODO: This index is currently not used. Does it make sense to introduce it anyway?
		// I still do not fully understand what MySQL does when a combined index is queried
		// with multiple lower/greater than clauses. Maybe separate indexes are better?
		$table->addIndex( array( 'value_lower_bound', 'value_upper_bound' ) );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof QuantityValue ) ) {
			throw new InvalidArgumentException( 'Value is not a QuantityValue.' );
		} elseif ( $value->getUnit() !== '1' ) {
			throw new InvalidArgumentException( 'Units other than "1" are not yet supported.' );
		}

		$values = array(
			'value_unit' => $this->normalizeUnit( $value->getUnit() ),
			'value_actual' => $value->getAmount()->getValue(),
			'value_lower_bound' => $value->getLowerBound()->getValue(),
			'value_upper_bound' => $value->getUpperBound()->getValue(),

			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getWhereConditions
	 *
	 * @param ValueDescription $description
	 *
	 * @return WhereConditions
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	public function getWhereConditions( ValueDescription $description ) {
		$value = $description->getValue();

		if ( !( $value instanceof QuantityValue ) ) {
			throw new InvalidArgumentException( 'Value is not a QuantityValue.' );
		} elseif ( $value->getUnit() !== '1' ) {
			throw new QueryNotSupportedException( $description, 'Units other than "1" are not yet supported.' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			return $this->getByQuantityConditions( $value );
		}

		return parent::getWhereConditions( $description );
	}

	private function getByQuantityConditions( QuantityValue $value ) {
		// Exact search if search range is zero.
		if ( $value->getLowerBound()->getValueFloat() >= $value->getUpperBound()->getValueFloat() ) {
			return $this->getSameValueConditions( $value->getAmount() );
		} else {
			return $this->getInRangeConditions( $value );
		}
	}

	private function getSameValueConditions( DecimalValue $value ) {
		$conditions = new WhereConditions();

		$conditions->addCondition( 'value_actual = :actual' );
		$conditions->setParameter( ':actual', $value->getValue() );

		return $conditions;
	}

	/**
	 * We are not asking if the given search value fits in a range, we are searching for
	 * values within the given search range.
	 *
	 * If searching for 1500 m (with default precision +/-1) we don't want to find 1501 m,
	 * but want to find 1500.99 m (no matter what the precision is).
	 * So the range is ]-precision,+precision[ (exclusive).
	 */
	private function getInRangeConditions( QuantityValue $value ) {
		$conditions = new WhereConditions();

		$conditions->addCondition( 'value_actual > :lower_bound' );
		$conditions->addCondition( 'value_actual < :upper_bound' );

		$conditions->setParameter( ':lower_bound', $value->getLowerBound()->getValue() );
		$conditions->setParameter( ':upper_bound', $value->getUpperBound()->getValue() );

		return $conditions;
	}

	/**
	 * @param string $unit
	 *
	 * @return string|null
	 */
	private function normalizeUnit( $unit ) {
		return $unit === '1' ? null : $unit;
	}

}
