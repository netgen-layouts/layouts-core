<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextLineMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new TextLineMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->mapper->getFormType());
    }
}
