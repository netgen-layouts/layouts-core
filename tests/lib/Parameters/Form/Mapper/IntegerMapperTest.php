<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\IntegerMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
