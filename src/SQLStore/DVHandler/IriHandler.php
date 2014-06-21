<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\IriValue;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * Represents the mapping between DataValues\IriValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IriHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 *
	 * @return string
	 */
	protected function getBaseTableName() {
		return 'iri';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		// TODO: figure out what the max field lengths should be
		$table->addColumn( 'value_scheme', Type::STRING );
		$table->addColumn( 'value_fragment', Type::STRING );
		$table->addColumn( 'value_query', Type::STRING );
		$table->addColumn( 'value_hierp', Type::STRING );
		$table->addColumn( 'value_iri', Type::STRING );
		$table->addColumn( 'value_json', Type::STRING );
	}

	/**
	 * @see DataValueHandler::getEqualityFieldName
	 *
	 * @return string
	 */
	public function getEqualityFieldName() {
		return 'value_json';
	}

	/**
	 * @see DataValueHandler::getSortFieldNames
	 *
	 * @return string[]
	 */
	public function getSortFieldNames() {
		return array( 'value_iri' );
	}

	/**
	 * @see DataValueHandler::getLabelFieldName
	 *
	 * @return string
	 */
	public function getLabelFieldName() {
		return 'value_iri';
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
		if ( !( $value instanceof IriValue ) ) {
			throw new InvalidArgumentException( 'Value is not a IriValue' );
		}

		$values = array(
			'value_scheme' => $value->getScheme(),
			'value_fragment' => $value->getFragment(),
			'value_query' => $value->getQuery(),
			'value_hierp' => $value->getHierarchicalPart(),

			'value_iri' => $value->getValue(),

			'value_json' => $this->getEqualityFieldValue( $value ),
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
		if ( !( $value instanceof IriValue ) ) {
			throw new InvalidArgumentException( 'Value is not a IriValue' );
		}

		return json_encode( $value->getArrayValue() );
	}

}
