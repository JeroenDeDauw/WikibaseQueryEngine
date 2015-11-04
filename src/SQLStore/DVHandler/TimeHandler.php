<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\TimeValue;
use DataValues\TimeValueCalculator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\QueryNotSupportedException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\WhereConditions;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland < jeroendedauw@gmail.com >
 * @author Thiemo Mättig
 */
class TimeHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 *
	 * @return string
	 */
	protected function getBaseTableName() {
		return 'time';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		// TODO: Do we need to store the precision, before, after, timezone and calendar model?
		$table->addColumn( 'value_timestamp',     Type::BIGINT );
		$table->addColumn( 'value_min_timestamp', Type::BIGINT );
		$table->addColumn( 'value_max_timestamp', Type::BIGINT );
		$table->addColumn( 'hash',                Type::STRING, array( 'length' => 32 ) );

		$table->addIndex( array( 'value_timestamp' ) );
		$table->addIndex( array( 'value_min_timestamp' ) );
		$table->addIndex( array( 'value_max_timestamp' ) );
	}

	/**
	 * @see DataValueHandler::getSortFieldNames
	 *
	 * @return string[]
	 */
	public function getSortFieldNames() {
		return array( 'value_timestamp' );
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
		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Value is not a TimeValue.' );
		}

		$calculator = new TimeValueCalculator();
		$timestamp = $calculator->getTimestamp( $value );
		$precisionInSeconds = $calculator->getSecondsForPrecision( $value->getPrecision() );

		$before = abs( $value->getBefore() );
		// The range from before to after must be at least one unit long
		$after = max( 1, abs( $value->getAfter() ) );

		$values = array(
			'value_timestamp' => $timestamp,
			'value_min_timestamp' => $timestamp - $before * $precisionInSeconds,
			'value_max_timestamp' => $timestamp + $after * $precisionInSeconds,

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

		if ( !( $value instanceof TimeValue ) ) {
			throw new InvalidArgumentException( 'Value is not a TimeValue.' );
		}

		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			return $this->getInRangeConditions( $value );
		}

		return parent::getWhereConditions( $description );
	}

	private function getInRangeConditions( TimeValue $value ) {
		$calculator = new TimeValueCalculator();
		$timestamp = $calculator->getTimestamp( $value );
		$precisionInSeconds = $calculator->getSecondsForPrecision( $value->getPrecision() );

		$before = abs( $value->getBefore() );
		// The range from before to after must be at least one unit long
		$after = max( 1, abs( $value->getAfter() ) );

		$conditions = new WhereConditions();

		// When searching for 1900 (precision year) we do not want to find 1901-01-01T00:00:00.
		$conditions->addCondition( 'value_timestamp >= :min_timestamp' );
		$conditions->addCondition( 'value_timestamp < :max_timestamp' );

		$conditions->setParameter( ':min_timestamp', $timestamp - $before * $precisionInSeconds );
		$conditions->setParameter( ':max_timestamp', $timestamp + $after * $precisionInSeconds );

		return $conditions;
	}

}
