<?php

namespace Wikibase\QueryEngine\SQLStore;

use OutOfBoundsException;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\IndexDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\QueryEngine\SQLStore\DVHandler\BooleanHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\EntityIdHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\GeoCoordinateHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\IriHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\MonolingualTextHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;

/**
 * A collection of DataValueHandler objects to be used by the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
final class DataValueHandlers {

	/**
	 * @since 0.1
	 *
	 * @var DataValueHandler[]
	 */
	private $dvHandlers;

	/**
	 * @since 0.1
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * Returns all DataValueHandler objects.
	 * Array keys are data value types pointing to the corresponding DataValueHandler.
	 *
	 * @since 0.1
	 *
	 * @return DataValueHandler[]
	 */
	public function getHandlers() {
		$this->initialize();

		return $this->dvHandlers;
	}

	/**
	 * Returns the DataValueHandler for the specified DataValue type.
	 * Note that this is not suited for interaction with the actual store schema,
	 * for that one should use the Schema class.
	 *
	 * @since 0.1
	 *
	 * @param string $dataValueType
	 *
	 * @return DataValueHandler
	 * @throws OutOfBoundsException
	 */
	public function getHandler( $dataValueType ) {
		$this->initialize();

		if ( !array_key_exists( $dataValueType, $this->dvHandlers ) ) {
			throw new OutOfBoundsException( "There is no DataValueHandler registered for DataValue type '$dataValueType'" );
		}

		return $this->dvHandlers[$dataValueType];
	}

	/**
	 * Initialize the object if not done so already.
	 *
	 * @since 0.1
	 */
	private function initialize() {
		if ( $this->initialized ) {
			return;
		}

		$this->dvHandlers = $this->getDefaultHandlers();

		// TODO: hook

		$this->initialized = true;
	}

	/**
	 * @since 0.1
	 *
	 * @return DataValueTable[]
	 */
	private function getDefaultHandlers() {
		$tables = array();

		$tables['boolean'] = new BooleanHandler( new DataValueTable(
			new TableDefinition(
				'boolean',
				array(
					new FieldDefinition(
						'value',
						new TypeDefinition( TypeDefinition::TYPE_TINYINT ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value',
			'value'
		) );

		$tables['string'] = new StringHandler( new DataValueTable(
			new TableDefinition(
				'string',
				array(
					new FieldDefinition( 'value',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value',
			'value',
			'value'
		) );

		$tables['monolingualtext'] = new MonolingualTextHandler( new DataValueTable(
			new TableDefinition(
				'mono_text',
				array(
					new FieldDefinition( 'value_text',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_language',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_json',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value_json',
			'value_text',
			'value_text'
		) );

		$tables['globecoordinate'] = new GeoCoordinateHandler( new DataValueTable(
			new TableDefinition(
				'geo',
				array(
					new FieldDefinition(
						'value_globe',
						new TypeDefinition( TypeDefinition::TYPE_VARCHAR, 255 ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_lat',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_lon',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_max_lat',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_min_lat',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_max_lon',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_min_lon',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_json',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				),
				array(
					new IndexDefinition(
						'value_lat',
						array( 'value_lat' => 0 )
					),
					new IndexDefinition(
						'value_lon',
						array( 'value_lon' => 0 )
					),
					new IndexDefinition(
						'value_min_lat',
						array( 'value_min_lat' => 0 )
					),
					new IndexDefinition(
						'value_max_lat',
						array( 'value_max_lat' => 0 )
					),
					new IndexDefinition(
						'value_min_lon',
						array( 'value_min_lon' => 0 )
					),
					new IndexDefinition(
						'value_max_lon',
						array( 'value_max_lon' => 0 )
					),
				)
			),
			'value_json',
			'value_lat'
		) );

		$tables['number'] = new NumberHandler( new DataValueTable(
			new TableDefinition(
				'number',
				array(
					new FieldDefinition(
						'value',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_lower_bound',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_upper_bound',
						new TypeDefinition( TypeDefinition::TYPE_DECIMAL ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_json',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				),
				array(
					new IndexDefinition(
						'value',
						array( 'value' => 0 )
					),
					new IndexDefinition(
						'value_lower_bound',
						array( 'value_lower_bound' => 0 )
					),
					new IndexDefinition(
						'value_upper_bound',
						array( 'value_upper_bound' => 0 )
					),
				)
			),
			'value_json',
			'value',
			'value'
		) );

		$tables['iri'] = new IriHandler( new DataValueTable(
			new TableDefinition(
				'iri',
				array(
					new FieldDefinition(
						'value_scheme',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_fragment',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_query',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_hierp',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),

					new FieldDefinition(
						'value_iri',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'value_json',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'value_json',
			'value_iri',
			'value_iri'
		) );

		// TODO: register via hook
		$tables['wikibase-entityid'] = new EntityIdHandler( new DataValueTable(
			new TableDefinition(
				'entityid',
				array(
					new FieldDefinition(
						'id',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
					new FieldDefinition(
						'type',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						FieldDefinition::NOT_NULL
					),
				)
			),
			'id',
			'id'
		) );

		//TODO wbq_<role>_time table

		return $tables;
	}

}
