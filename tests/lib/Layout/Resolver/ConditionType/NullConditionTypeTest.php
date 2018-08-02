<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->conditionType = new NullConditionType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType::getType
     */
    public function testGetType(): void
    {
        $this->assertSame('null', $this->conditionType::getType());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType::getConstraints
     */
    public function testGetConstraints(): void
    {
        $this->assertSame([], $this->conditionType->getConstraints());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType::matches
     */
    public function testMatches(): void
    {
        $this->assertTrue($this->conditionType->matches(Request::create(''), ''));
    }
}
