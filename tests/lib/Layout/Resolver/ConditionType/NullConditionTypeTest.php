<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class NullConditionTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType
     */
    private $conditionType;

    public function setUp()
    {
        $this->conditionType = new NullConditionType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('null', $this->conditionType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType::getConstraints
     */
    public function testGetConstraints()
    {
        $this->assertEquals([], $this->conditionType->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType::matches
     */
    public function testMatches()
    {
        $this->assertTrue($this->conditionType->matches(Request::create(''), ''));
    }
}
