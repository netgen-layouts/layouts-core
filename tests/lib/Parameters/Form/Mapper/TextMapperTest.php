<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\TextMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class TextMapperTest extends TestCase
{
    private TextMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new TextMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\TextMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextareaType::class, $this->mapper->getFormType());
    }
}
