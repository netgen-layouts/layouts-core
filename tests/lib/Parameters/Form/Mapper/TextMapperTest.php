<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\TextMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class TextMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\TextMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new TextMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\TextMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(TextareaType::class, $this->mapper->getFormType());
    }
}
