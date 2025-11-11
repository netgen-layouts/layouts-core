<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\IntegerMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

#[CoversClass(IntegerMapper::class)]
final class IntegerMapperTest extends TestCase
{
    private IntegerMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new IntegerMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(IntegerType::class, $this->mapper->getFormType());
    }
}
