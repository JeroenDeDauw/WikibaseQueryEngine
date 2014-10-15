<?php

namespace Wikibase\QueryEngine\Tests\Integration;

use DataValues\StringValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\QueryEngine\NullMessageReporter;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;

/**
 * @group large
 * @group performance
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PerformanceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SQLStoreWithDependencies
	 */
	private $store;

	public function setUp() {
		$this->store = IntegrationStoreBuilder::newStore( $this );

		$this->store->newInstaller()->install();
	}

	public function tearDown() {
		if ( isset( $this->store ) ) {
			$this->store->newUninstaller( new NullMessageReporter() )->uninstall();
		}
	}

	public function testItemInsertion() {
		$writer = $this->store->newWriter();
		$generator = new RandomItemIterator( new RandomItemBuilder(), 250 );

		/**
		 * @var Item $item
		 */
		foreach ( $generator as $item ) {
			$writer->insertEntity( $item );
		}

		$this->assertTrue( true );
	}

}

/**
 * Builds random items using the provided random item builder.
 * The ids of the items are sequential starting with Q1.
 * The iterator can by default generate an unlimited amount
 * of Items. An upper bound can also be specified in the constructor.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RandomItemIterator implements \Iterator {

	private $itemBuilder;
	private $maxElements;

	private $number = 0;
	private $current;

	public function __construct( RandomItemBuilder $itemBuilder, $maxElements = 0 ) {
		$this->itemBuilder = $itemBuilder;
		$this->maxElements = $maxElements;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		return $this->current;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		$this->current = $this->itemBuilder->newItem( ItemId::newFromNumber( ++$this->number ) );
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		return $this->number;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 *
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return $this->maxElements === 0 || $this->number <= $this->maxElements;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		$this->number = 0;
		$this->next();
	}

}

class RandomItemBuilder {

	public function newItem( ItemId $id = null ) {
		$item = Item::newEmpty();
		$item->setId( $id );

		$this->addStatements( $item );

		return $item;
	}

	private function addStatements( Item $item ) {
		$i = mt_rand( 0, 200 );

		while ( $i-- > 0 ) {
			$this->addStatement( $item );
		}
	}

	private function addStatement( Item $item ) {
		$statement = new Statement( new Claim( $this->newSnak() ) );

		$statement->setGuid( $item->getId()->getSerialization() . uniqid() );
		$item->addClaim( $statement );
	}

	private function newSnak() {
		return new PropertyValueSnak(
			PropertyId::newFromNumber( mt_rand( 1, 100 ) ),
			$this->newDataValue()
		);
	}

	private function newDataValue() {
//		switch ( mt_rand( 0, 1 ) ) {
//			case 0:
				return new StringValue( str_repeat( uniqid(), 5 ) );
//			case 1:
//				return new NumberValue( mt_rand( -31337, 31337 ) );
//		}

//		throw new \LogicException();
	}

}