<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\HtmlMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\Framework\TestCase;

class HtmlMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\HtmlMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new HtmlMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\HtmlMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextareaType::class, $this->mapper->getFormType());
    }
}
