<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type\TimeType;
use PHPUnit\Framework\TestCase;

final class TimeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new Time();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TimeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(['label' => false], $this->mapper->getFormOptions());
    }
}
