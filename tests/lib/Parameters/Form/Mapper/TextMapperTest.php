<?php

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

    public function setUp()
    {
        $this->mapper = new TextMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\TextMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextareaType::class, $this->mapper->getFormType());
    }
}
