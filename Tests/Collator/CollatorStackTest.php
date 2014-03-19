<?php

namespace Markup\NeedleBundle\Tests\Collator;

use Markup\NeedleBundle\Collator\CollatorStack;
use Mockery as m;

/**
 * Test for a collator that composes a stack of typed collators (i.e. the order of the collators is significant, and a value of an earlier type will be sorted to before one of a later type).
 */
class CollatorStackTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->stack = new CollatorStack();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsCollator()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Collator\CollatorInterface', $this->stack);
    }

    public function testFallsBackToSimpleStringCompareWithEmptyStack()
    {
        $list = array('this', 'old', 'house', 'is', 'sinking');
        @usort($list, array($this->stack, 'compare'));//at-suppressor is because of annoying PHP bug @see https://bugs.php.net/bug.php?id=50688
        $this->assertEquals(array('house', 'is', 'old', 'sinking', 'this'), $list);
    }

    public function testPushedCollatorSortsValues()
    {
        $list = array('1', '2', '12', '3', '23');
        $collator = m::mock('Markup\NeedleBundle\Collator\TypedCollatorInterface');
        $collator
            ->shouldReceive('hasTypeFor')
            ->andReturn(true);
        $collator
            ->shouldReceive('compare')
            ->andReturnUsing(function ($value1, $value2) {
                return floatval($value1) - floatval($value2);
            });
        $this->stack->push($collator);
        @usort($list, array($this->stack, 'compare'));//at-suppressor is because of annoying PHP bug @see https://bugs.php.net/bug.php?id=50688
        $this->assertEquals(array('1', '2', '3', '12', '23'), $list);
    }

    public function testComparisonHappensInOrderOfCollator()
    {
        $value1 = 'eskimo';
        $value2 = 'igloo';
        $list = array($value1, $value2);
        $collator1 = m::mock('Markup\NeedleBundle\Collator\TypedCollatorInterface')->shouldIgnoreMissing();
        $collator1
            ->shouldReceive('hasTypeFor')
            ->with($value1)
            ->andReturn(true);
        $collator1
            ->shouldReceive('hasTypeFor')
            ->with($value2)
            ->andReturn(false);
        $collator1
            ->shouldReceive('compare')
            ->andReturnUsing('strcasecmp');
        $collator2 = m::mock('Markup\NeedleBundle\Collator\TypedCollatorInterface')->shouldIgnoreMissing();
        $collator2
            ->shouldReceive('hasTypeFor')
            ->with($value2)
            ->andReturn(true);
        $collator2
            ->shouldReceive('hasTypeFor')
            ->with($value1)
            ->andReturn(false);
        $collator2
            ->shouldReceive('compare')
            ->andReturnUsing('strcasecmp');
        $this->stack->push($collator1);
        $this->stack->push($collator2);
        @usort($list, array($this->stack, 'compare'));//at-suppressor is because of annoying PHP bug @see https://bugs.php.net/bug.php?id=50688
        $this->assertEquals(array($value1, $value2), $list);
    }

    public function testValuesOfUnknownTypeComeAfterOthersByDefault()
    {
        $typedValue = 'yesplease';
        $untypedValue = 'nothankyou';
        $list = array($typedValue, $untypedValue);
        $collator = m::mock('Markup\NeedleBundle\Collator\TypedCollatorInterface')->shouldIgnoreMissing();
        $collator
            ->shouldReceive('hasTypeFor')
            ->with($typedValue)
            ->andReturn(true);
        $collator
            ->shouldReceive('hasTypeFor')
            ->with($untypedValue)
            ->andReturn(false);
        $this->stack->push($collator);
        @usort($list, array($this->stack, 'compare'));//at-suppressor is because of annoying PHP bug @see https://bugs.php.net/bug.php?id=50688
        $this->assertEquals(array($typedValue, $untypedValue), $list);
    }

    public function testValuesOfUnknownTypeComeBeforeIfSelected()
    {
        $this->stack->setUntypedValuesToPrecede(true);
        $typedValue = 'yesplease';
        $untypedValue = 'nothankyou';
        $list = array($typedValue, $untypedValue);
        $collator = m::mock('Markup\NeedleBundle\Collator\TypedCollatorInterface')->shouldIgnoreMissing();
        $collator
            ->shouldReceive('hasTypeFor')
            ->with($typedValue)
            ->andReturn(true);
        $collator
            ->shouldReceive('hasTypeFor')
            ->with($untypedValue)
            ->andReturn(false);
        $this->stack->push($collator);
        @usort($list, array($this->stack, 'compare'));//at-suppressor is because of annoying PHP bug @see https://bugs.php.net/bug.php?id=50688
        $this->assertEquals(array($untypedValue, $typedValue), $list);
    }

    public function testCompareHappensWithCorrectlyIndexedCollator()
    {
        $collator1 = m::mock('Markup\NeedleBundle\Collator\TypedCollatorInterface')->shouldIgnoreMissing();
        $collator2 = m::mock('Markup\NeedleBundle\Collator\TypedCollatorInterface')->shouldIgnoreMissing();
        $collator1
            ->shouldReceive('hasTypeFor')
            ->andReturn(false);
        $collator2
            ->shouldReceive('hasTypeFor')
            ->andReturn(true);
        $collatorResult1 = 1;
        $collatorResult2 = -1;
        $collator1
            ->shouldReceive('compare')
            ->andReturn($collatorResult1);
        $collator2
            ->shouldReceive('compare')
            ->andReturn($collatorResult2);
        $this->stack->push($collator1);
        $this->stack->push($collator2);
        $this->assertEquals($collatorResult2, $this->stack->compare('this', 'that'));
    }
}
