<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\Engine\Interpreter;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Wikibase\QueryEngine\SQLStore\Engine\Interpreter\DispatchingInterpreter;
use Wikibase\QueryEngine\Tests\Fixtures\FakeAnyValueInterpreter;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Engine\Interpreter\DispatchingInterpreter
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DispatchingInterpreterTest extends \PHPUnit_Framework_TestCase {

	public function testGivenNoInterpreters_canInterpretReturnsFalse() {
		$interpreter = new DispatchingInterpreter();

		$this->assertFalse( $interpreter->canInterpretDescription( new AnyValue() ) );
		$this->assertFalse( $interpreter->canInterpretDescription( new Conjunction( [] ) ) );
	}

	public function testGivenNoInterpreters_interpretDescriptionThrowsException() {
		$interpreter = new DispatchingInterpreter();

		$this->setExpectedException( 'InvalidArgumentException' );
		$interpreter->interpretDescription( new AnyValue() );
	}

	public function testWhenNoInterpretersMatch_canInterpretReturnsFalse() {
		$interpreter = new DispatchingInterpreter();
		$interpreter->addInterpreter( new FakeAnyValueInterpreter() );

		$this->assertFalse( $interpreter->canInterpretDescription( new Conjunction( [] ) ) );
	}

	public function testWhenAnInterpreterMatches_canInterpretReturnsTrue() {
		$interpreter = new DispatchingInterpreter();
		$interpreter->addInterpreter( new FakeAnyValueInterpreter() );

		$this->assertTrue( $interpreter->canInterpretDescription( new AnyValue() ) );
	}

	public function testWhenNoInterpretersMatch_interpretDescriptionThrowsException() {
		$interpreter = new DispatchingInterpreter();
		$interpreter->addInterpreter( new FakeAnyValueInterpreter() );

		$this->setExpectedException( 'InvalidArgumentException' );
		$interpreter->interpretDescription( new Conjunction( [] ) );
	}

	public function testWhenAnInterpreterMatches_interpretDescriptionReturnsSqlQueryPart() {
		$interpreter = new DispatchingInterpreter();
		$interpreter->addInterpreter( new FakeAnyValueInterpreter() );

		$this->assertInstanceOf(
			'Wikibase\QueryEngine\SQLStore\Engine\SqlQueryPart',
			$interpreter->interpretDescription( new AnyValue() )
		);
	}

}
