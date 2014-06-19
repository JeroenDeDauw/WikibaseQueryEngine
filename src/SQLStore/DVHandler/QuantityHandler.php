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
		$table->addColumn( $this->getEqualityFieldName(), Type::STRING, array( 'length' => 32 ) );
		$table->addColumn( 'value_actual', Type::DECIMAL );
		$table->addColumn( 'value_lower_bound', Type::DECIMAL );
		$table->addColumn( 'value_upper_bound', Type::DECIMAL );

		$table->addIndex( array( 'value_actual' ) );
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
		return 'hash';
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

			$this->getEqualityFieldName() => $this->getEqualityFieldValue( $value ),
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
		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			$searchValue = $description->getValue();
			if ( !( $searchValue instanceof QuantityValue ) ) {
				throw new InvalidArgumentException( 'Value is not a QuantityValue.' );
			}

			// We are not asking if the given search value fits in a range, we are searching for
			// values within the given search range.
			$lowerBound = $searchValue->getLowerBound()->getValueFloat();
			$upperBound = $searchValue->getUpperBound()->getValueFloat();

			// Exact search if search range is zero.
			if ( $lowerBound >= $upperBound ) {
				$builder->andWhere( $this->getTableName() . '.value_actual = :actual' );
				$builder->setParameter( ':actual', $searchValue->getAmount()->getValueFloat() );
			} else {
				// If searching for 1500 m (with default precision +/-1) we don't want to
				// find 1501 m, but want to find 1500.99 m (no matter what the precision is).
				// So the range is ]-precision,+precision[ (exclusive).
				$builder->andWhere( $this->getTableName() . '.' . $this->getValueFieldName() . ' > :lower_bound' );
				$builder->andWhere( $this->getTableName() . '.' . $this->getValueFieldName() . ' < :upper_bound' );
				$builder->setParameter( ':lower_bound', $lowerBound );
				$builder->setParameter( ':upper_bound', $upperBound );
			}
		} else {
			parent::addMatchConditions( $builder, $description );
		}
	}

}
