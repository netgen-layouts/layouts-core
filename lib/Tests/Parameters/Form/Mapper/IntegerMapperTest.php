<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\IntegerMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use PHPUnit\Framework\TestCase;

class IntegerMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\IntegerMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new IntegerMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\IntegerMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(IntegerType::class, $this->mapper->getFormType());
    }
}
