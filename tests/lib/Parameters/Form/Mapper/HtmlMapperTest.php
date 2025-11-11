<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\HtmlMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

#[CoversClass(HtmlMapper::class)]
final class HtmlMapperTest extends TestCase
{
    private HtmlMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new HtmlMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextareaType::class, $this->mapper->getFormType());
    }
}
