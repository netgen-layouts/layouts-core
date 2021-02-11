<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Time;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType;
use PHPUnit\Framework\TestCase;

final class TimeTest extends TestCase
{
    private Time $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Time();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TimeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(['label' => false], $this->mapper->getFormOptions());
    }
}
