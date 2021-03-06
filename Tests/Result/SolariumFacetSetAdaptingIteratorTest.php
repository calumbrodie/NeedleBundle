<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\SolariumFacetSetAdaptingIterator;

/**
* A test for an iterator that can wrap a Solarium facet set and emit generic facet values.
*/
class SolariumFacetSetAdaptingIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFacetSetIterator()
    {
        $refl = new \ReflectionClass('Markup\NeedleBundle\Result\SolariumFacetSetAdaptingIterator');
        $this->assertTrue($refl->implementsInterface('Markup\NeedleBundle\Facet\FacetSetIteratorInterface'));
    }

    public function testIterate()
    {
        $value = 'red';
        $count = 5;
        $solariumFacetField = $this->getMockBuilder('Solarium\QueryType\Select\Result\Facet\Field')
            ->disableOriginalConstructor()
            ->getMock();
        $valuesIterator = new \ArrayIterator(array($value => $count));
        $solariumFacetField
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($valuesIterator));
        $it = new SolariumFacetSetAdaptingIterator($solariumFacetField);
        $values = iterator_to_array($it);
        $this->assertCount(1, $values, 'checking there is one value');
        $this->assertContainsOnly('Markup\NeedleBundle\Facet\FacetValueInterface', $values);
        foreach ($values as $singleValue) {
            break;
        }
        $this->assertEquals($value, $singleValue->getValue());
        $this->assertCount($count, $singleValue);
    }

    public function testIteratorWithCollation()
    {
        $values = array(
            'red'               => 1,
            'blue'              => 2,
            'yellow'            => 3,
        );
        $expectedValues = array_reverse($values);
        $collator = $this->getMock('Markup\NeedleBundle\Collator\CollatorInterface');
        $collator
            ->expects($this->any())
            ->method('compare')
            ->will($this->returnValue(-1));
        $it = new SolariumFacetSetAdaptingIterator($values, $collator);
        $this->assertEquals(array_keys($expectedValues), array_keys(iterator_to_array($it)));
    }

    public function testCount()
    {
        $values = array(
            'red'               => 1,
            'blue'              => 2,
            'yellow'            => 3,
        );
        $it = new SolariumFacetSetAdaptingIterator($values);
        $this->assertCount(3, $it);
    }
}
