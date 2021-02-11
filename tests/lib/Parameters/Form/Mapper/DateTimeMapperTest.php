<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Form\DateTimeType;
use Netgen\Layouts\Parameters\Form\Mapper\DateTimeMapper;
use PHPUnit\Framework\TestCase;

final class DateTimeMapperTest extends TestCase
{
    private DateTimeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DateTimeMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\DateTimeMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(DateTimeType::class, $this->mapper->getFormType());
    }
}
