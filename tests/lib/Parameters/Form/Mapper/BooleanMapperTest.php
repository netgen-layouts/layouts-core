<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\BooleanMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

#[CoversClass(BooleanMapper::class)]
final class BooleanMapperTest extends TestCase
{
    private BooleanMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new BooleanMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(CheckboxType::class, $this->mapper->getFormType());
    }
}
