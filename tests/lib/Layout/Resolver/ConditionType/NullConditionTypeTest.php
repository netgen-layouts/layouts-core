<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\ConditionType;

use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class NullConditionTypeTest extends TestCase
{
    private NullConditionType $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new NullConditionType();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType::getType
     */
    public function testGetType(): void
    {
        self::assertSame('null', $this->conditionType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType::getConstraints
     */
    public function testGetConstraints(): void
    {
        self::assertSame([], $this->conditionType->getConstraints());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType::matches
     */
    public function testMatches(): void
    {
        self::assertTrue($this->conditionType->matches(Request::create(''), ''));
    }
}
