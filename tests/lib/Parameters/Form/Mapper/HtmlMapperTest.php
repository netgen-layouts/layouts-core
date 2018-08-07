<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\HtmlMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class HtmlMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\HtmlMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new HtmlMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\HtmlMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextareaType::class, $this->mapper->getFormType());
    }
}
