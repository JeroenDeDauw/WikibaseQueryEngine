<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore;

use Wikibase\QueryEngine\SQLStore\WhereConditions;

/**
 * @covers Wikibase\QueryEngine\SQLStore\WhereConditions
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WhereConditionsTest extends \PHPUnit_Framework_TestCase {

	public function testGivenEmptyWhereConditions_getConditionsReturnsEmptyArray() {
		$this->assertSame( [], ( new WhereConditions() )->getConditions() );
	}

	public function testGivenEmptyWhereConditions_getParametersReturnsEmptyArray() {
		$this->assertSame( [], ( new WhereConditions() )->getParameters() );
	}

	public function testAfterAddingConditions_getConditionsReturnsThem() {
		$conditions = new WhereConditions();

		$conditions->addCondition( 'awesomeness >= 9001' );
		$conditions->addCondition( 'lameness IS NULL' );
		$conditions->addCondition( 'buzzword = :buzzword' );

		$this->assertSame(
			[
				'awesomeness >= 9001',
				'lameness IS NULL',
				'buzzword = :buzzword'
			],
			$conditions->getConditions()
		);
	}

	public function testAfterSettingParameters_getParametersReturnsThem() {
		$conditions = new WhereConditions();

		$conditions->setParameter( ':foo', 42 );
		$conditions->setParameter( ':bar', 'baz' );

		$this->assertSame(
			[
				':foo' => 42,
				':bar' => 'baz'
			],
			$conditions->getParameters()
		);
	}

	public function testAfterSettingEquality_bothConditionsAndParametersGotSet() {
		$conditions = new WhereConditions();

		$conditions->setEquality( 'bunnies', 'fluffy' );
		$conditions->setEquality( 'answer', 42 );

		$this->assertSame(
			[
				'bunnies = :bunnies',
				'answer = :answer'
			],
			$conditions->getConditions()
		);

		$this->assertSame(
			[
				':bunnies' => 'fluffy',
				':answer' => 42
			],
			$conditions->getParameters()
		);
	}

}
