<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\RuleLoader;

use Netgen\BlockManager\Layout\Resolver\Rule;
use Netgen\BlockManager\Layout\Resolver\RuleBuilder\RuleBuilderInterface;
use Netgen\BlockManager\Layout\Resolver\RuleHandler\RuleHandlerInterface;
use Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoader;
use Netgen\BlockManager\Layout\Resolver\Target;

class RuleLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleBuilderMock;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoaderInterface
     */
    protected $ruleLoader;

    public function setUp()
    {
        $this->ruleHandlerMock = $this->getMock(
            RuleHandlerInterface::class
        );

        $this->ruleBuilderMock = $this->getMock(
            RuleBuilderInterface::class
        );

        $this->ruleLoader = new RuleLoader(
            $this->ruleHandlerMock,
            $this->ruleBuilderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoader::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoader::loadRules
     */
    public function testLoadRules()
    {
        $target = new Target('target', array(42));
        $rule = new Rule(42, $target);

        $this->ruleHandlerMock
            ->expects($this->once())
            ->method('loadRules')
            ->with($this->equalTo($target->getIdentifier()), $this->equalTo($target->getValues()))
            ->will($this->returnValue(array('some_data')));

        $this->ruleBuilderMock
            ->expects($this->once())
            ->method('buildRules')
            ->with($this->equalTo($target), $this->equalTo(array('some_data')))
            ->will($this->returnValue(array($rule)));

        self::assertEquals(array($rule), $this->ruleLoader->loadRules($target));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoader::loadRules
     */
    public function testLoadRulesWithEmptyValues()
    {
        $target = new Target('target', array());

        $this->ruleHandlerMock
            ->expects($this->never())
            ->method('loadRules');

        $this->ruleBuilderMock
            ->expects($this->never())
            ->method('buildRules');

        self::assertEquals(array(), $this->ruleLoader->loadRules($target));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoader::loadRules
     */
    public function testLoadRulesWithNoDataFound()
    {
        $target = new Target('target', array(42));

        $this->ruleHandlerMock
            ->expects($this->once())
            ->method('loadRules')
            ->with($this->equalTo($target->getIdentifier()), $this->equalTo($target->getValues()))
            ->will($this->returnValue(array()));

        $this->ruleBuilderMock
            ->expects($this->never())
            ->method('buildRules');

        self::assertEquals(array(), $this->ruleLoader->loadRules($target));
    }
}
