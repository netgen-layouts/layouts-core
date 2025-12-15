<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\TimeMapper;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TimeMapper::class)]
final class TimeMapperTest extends TestCase
{
    private TimeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new TimeMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TimeType::class, $this->mapper->getFormType());
    }

    public function testGetFormOptions(): void
    {
        self::assertSame(['label' => false], $this->mapper->getFormOptions());
    }
}
