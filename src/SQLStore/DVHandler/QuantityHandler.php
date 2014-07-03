<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\QuantityValue;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.1
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
			throw new InvalidArgumentException( 'Value is not a QuantityValue' );
		}

		$values = array(
			'value_actual' => $value->getAmount()->getValue(),
			'value_lower_bound' => $value->getLowerBound()->getValue(),
			'value_upper_bound' => $value->getUpperBound()->getValue(),

			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::addMatchConditions
	 *
	 * @param QueryBuilder $builder
	 * @param ValueDescription $description
	 *
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	public function addMatchConditions( QueryBuilder $builder, ValueDescription $description ) {
		$value = $description->getValue();

		if ( !( $value instanceof QuantityValue ) ) {
			throw new InvalidArgumentException( 'Value is not a QuantityValue.' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			// We are not asking if the given search value fits in a range, we are searching for
			// values within the given search range.
			$lowerBound = $value->getLowerBound();
			$upperBound = $value->getUpperBound();

			// Exact search if search range is zero.
			if ( $lowerBound->getValueFloat() >= $upperBound->getValueFloat() ) {
				$builder->andWhere( $this->getTableName() . '.value_actual = :actual' );
				$builder->setParameter( ':actual', $value->getAmount()->getValue() );
			} else {
				// If searching for 1500 m (with default precision +/-1) we don't want to
				// find 1501 m, but want to find 1500.99 m (no matter what the precision is).
				// So the range is ]-precision,+precision[ (exclusive).
				$builder->andWhere( $this->getTableName() . '.value_actual > :lower_bound' );
				$builder->andWhere( $this->getTableName() . '.value_actual < :upper_bound' );
				$builder->setParameter( ':lower_bound', $lowerBound->getValue() );
				$builder->setParameter( ':upper_bound', $upperBound->getValue() );
			}
		} else {
			parent::addMatchConditions( $builder, $description );
		}
	}

}
