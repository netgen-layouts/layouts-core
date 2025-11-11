<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\TextLineMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[CoversClass(TextLineMapper::class)]
final class TextLineMapperTest extends TestCase
{
    private TextLineMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new TextLineMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
