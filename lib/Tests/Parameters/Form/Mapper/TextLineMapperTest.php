<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class TextLineMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper
     */
    protected $mapper;

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
