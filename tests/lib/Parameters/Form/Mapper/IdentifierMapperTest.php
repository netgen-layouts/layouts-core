<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\IdentifierMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
