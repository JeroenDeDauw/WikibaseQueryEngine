<?php

namespace Wikibase\QueryEngine\SQLStore\ClaimStore;

use InvalidArgumentException;
use Wikibase\Database\QueryInterface;

/**
 * Interface to the claims table.
 *
 * This is not a "claims store" since it does not store whole claims,
 * merely their id mapping and some other non-claim-value information.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ClaimsTable {

	protected $queryInterface;
	protected $tableName;

	/**
	 * @param QueryInterface $queryInterface
	 * @param string $tableName
	 */
	public function __construct( QueryInterface $queryInterface, $tableName ) {
		$this->queryInterface = $queryInterface;
		$this->tableName = $tableName;
	}

	public function insertClaimRow( ClaimRow $claimRow ) {
		if ( $claimRow->getInternalId() !== null ) {
			throw new InvalidArgumentException( 'Cannot insert a ClaimRow that already has an ID' );
		}

		$this->queryInterface->insert(
			$this->tableName,
			$this->getWriteValues( $claimRow )
		);

		return $this->queryInterface->getInsertId();
	}

	protected function getWriteValues( ClaimRow $claimRow ) {
		return array(
			'guid' => $claimRow->getExternalGuid(),
			'subject_id' => $claimRow->getInternalSubjectId(),
			'property_id' => $claimRow->getInternalPropertyId(),
			'rank' => $claimRow->getRank(),
			'hash' => $claimRow->getHash(),
		);
	}

	public function removeClaimsOfSubject( $internalSubjectId ) {
		$this->queryInterface->delete(
			$this->tableName,
			array(
				'subject_id' => $internalSubjectId
			)
		);
	}

}
