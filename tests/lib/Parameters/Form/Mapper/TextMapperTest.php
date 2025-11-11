<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\TextMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

#[CoversClass(TextMapper::class)]
final class TextMapperTest extends TestCase
{
    private TextMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new TextMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextareaType::class, $this->mapper->getFormType());
    }
}
