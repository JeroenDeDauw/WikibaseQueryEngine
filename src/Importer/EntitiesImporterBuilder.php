<?php

namespace Wikibase\QueryEngine\Importer;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntitiesImporterBuilder {

	/**
	 * @param int $maxBatchSize
	 */
	public function setBatchSize( $maxBatchSize );

	/**
	 * @param $reporter ImportReporter
	 */
	public function setReporter( ImportReporter $reporter );

	/**
	 * @param string $previousEntityId
	 */
	public function setContinuationId( $previousEntityId );

	/**
	 * @param int $limit
	 */
	public function setLimit( $limit );

	/**
	 * @return EntitiesImporter
	 */
	public function newImporter();

}
