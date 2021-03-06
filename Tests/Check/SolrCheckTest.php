<?php

namespace Markup\NeedleBundle\Tests\Check;

use Markup\NeedleBundle\Check\SolrCheck;

/**
* A test for a monitor check object that can check whether a Solr instance is available.
*/
class SolrCheckTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->solariumClient = $this->getMockBuilder('Solarium\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->check = new SolrCheck($this->solariumClient);
    }

    public function testIsCheck()
    {
        $this->assertTrue($this->check instanceof \Liip\Monitor\Check\CheckInterface);
    }

    public function testCheckWhenOK()
    {
        $solrPing = $this->getMock('Solarium\Core\Query\QueryInterface');
        $this->solariumClient
            ->expects($this->any())
            ->method('createPing')
            ->will($this->returnValue($solrPing));
        $result = 'OK';
        $this->solariumClient
            ->expects($this->any())
            ->method('ping')
            ->with($this->equalTo($solrPing))
            ->will($this->returnValue($result));
        $checkResult = $this->check->check();
        $this->assertTrue($checkResult instanceof \Liip\Monitor\Result\CheckResult, '->check() returns a check result object');
        $this->assertEquals(\Liip\Monitor\Result\CheckResult::OK, $checkResult->getStatus());
    }

    public function testCheckWhenFail()
    {
        $solrPing = $this->getMock('Solarium\Core\Query\QueryInterface');
        $this->solariumClient
            ->expects($this->any())
            ->method('createPing')
            ->will($this->returnValue($solrPing));
        $this->solariumClient
            ->expects($this->any())
            ->method('ping')
            ->with($this->equalTo($solrPing))
            ->will($this->throwException(new \Solarium\Exception\RuntimeException()));
        $checkResult = $this->check->check();
        $this->assertTrue($checkResult instanceof \Liip\Monitor\Result\CheckResult, '->check() returns a check result object');
        $this->assertEquals(\Liip\Monitor\Result\CheckResult::CRITICAL, $checkResult->getStatus());
    }
}
