<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use PHPUnit\Framework\TestCase;

class NumberMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new NumberMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\NumberMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(NumberType::class, $this->mapper->getFormType());
    }
}
