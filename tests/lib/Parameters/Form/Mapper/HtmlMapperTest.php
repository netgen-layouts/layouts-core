<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\HtmlMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
