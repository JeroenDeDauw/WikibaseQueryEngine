<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\IriValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\StringHasher;

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
	 * @var StringHasher|null
	 */
	private $stringHasher = null;

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
	 *
	 * @param Table $table
	 */
	protected function completeTable( Table $table ) {
		// TODO: figure out what the max field lengths should be
		$table->addColumn( 'value_scheme',       Type::STRING, array( 'length' => 255 ) );
		$table->addColumn( 'value_hierarchical', Type::STRING, array( 'length' => 255 ) );
		$table->addColumn( 'value_query',        Type::STRING, array( 'length' => 255 ) );
		$table->addColumn( 'value_fragment',     Type::STRING, array( 'length' => 255 ) );
		$table->addColumn( 'hash',               Type::STRING, array( 'length' => StringHasher::LENGTH ) );

		// TODO: Is an index on the first 255 bytes/chars of each BLOB/CLOB column possible?
		$table->addIndex( array( 'hash' ) );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return string[]
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof IriValue ) ) {
			throw new InvalidArgumentException( 'Value is not a IriValue.' );
		}

		$values = array(
			'value_scheme' => $value->getScheme(),
			'value_hierarchical' => $value->getHierarchicalPart(),
			'value_query' => $value->getQuery(),
			'value_fragment' => $value->getFragment(),

			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getEqualityFieldValue
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof IriValue ) ) {
			throw new InvalidArgumentException( 'Value is not a IriValue.' );
		}

		return $this->hash( $value->getValue() );
	}

	private function hash( $string ) {
		if ( $this->stringHasher === null ) {
			$this->stringHasher = new StringHasher();
		}

		return $this->stringHasher->hash( $string );
	}

}
