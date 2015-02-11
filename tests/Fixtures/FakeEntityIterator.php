<?php

namespace Wikibase\QueryEngine\Tests\Fixtures;

use ArrayIterator;
use Wikibase\DataModel\Entity\Item;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FakeEntityIterator extends ArrayIterator {

	private $delayInMilliseconds = 0;

	public function __construct() {
		parent::__construct( array() );

		foreach ( range( 1337, 1347 ) as $number ) {
			$item = new Item();
			$item->setId( $number );
			$this->append( $item );
		}
	}

	public function setFakeDelayInMilliseconds( $delay ) {
		$this->delayInMilliseconds = $delay;
	}

	public function current() {
		if ( $this->delayInMilliseconds > 0 ) {
			usleep( $this->delayInMilliseconds );
		}

		return parent::current();
	}

}
