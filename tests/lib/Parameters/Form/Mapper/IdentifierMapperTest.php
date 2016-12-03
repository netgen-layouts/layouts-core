<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\IdentifierMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class IdentifierMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\IdentifierMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new IdentifierMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\IdentifierMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->mapper->getFormType());
    }
}
