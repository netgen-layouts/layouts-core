<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Form\DateTimeType;
use Netgen\Layouts\Parameters\Form\Mapper\DateTimeMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateTimeMapper::class)]
final class DateTimeMapperTest extends TestCase
{
    private DateTimeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DateTimeMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(DateTimeType::class, $this->mapper->getFormType());
    }
}
