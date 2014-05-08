<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\SnakRole;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\SnakStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SnakStoreTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @return SnakStore
	 */
	protected abstract function getInstance();

	protected abstract function canStoreProvider();

	protected abstract function cannotStoreProvider();

	public function differentSnaksProvider() {
		$argLists = array();

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P2',
			SnakRole::QUALIFIER,
			new ItemId( 'Q3' ),
			Claim::RANK_NORMAL
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P4',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q5' ),
			Claim::RANK_NORMAL
		) );

		$argLists[] = array( new ValueSnakRow(
			new StringValue( '~=[,,_,,]:3' ),
			'P31337',
			SnakRole::MAIN_SNAK,
			new ItemId( 'Q9001' ),
			Claim::RANK_NORMAL
		) );

		return $argLists;
	}

	/**
	 * @dataProvider differentSnaksProvider
	 */
	public function testReturnTypeOfCanUse( SnakRow $snak ) {
		$canStore = $this->getInstance()->canStore( $snak );
		$this->assertInternalType( 'boolean', $canStore );
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testCanStore( SnakRow $snak ) {
		$this->assertTrue( $this->getInstance()->canStore( $snak ) );
	}

	/**
	 * @dataProvider cannotStoreProvider
	 */
	public function testCannotStore( SnakRow $snak ) {
		$this->assertFalse( $this->getInstance()->canStore( $snak ) );
	}

	/**
	 * @dataProvider cannotStoreProvider
	 */
	public function testStoreSnakWithWrongSnakType( SnakRow $snakRow ) {
		$this->setExpectedException( 'InvalidArgumentException' );

		$this->getInstance()->storeSnakRow( $snakRow );
	}

}
