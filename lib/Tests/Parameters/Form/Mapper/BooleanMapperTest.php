<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\BooleanMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use PHPUnit\Framework\TestCase;

class BooleanMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\BooleanMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new BooleanMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\BooleanMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(CheckboxType::class, $this->mapper->getFormType());
    }
}
