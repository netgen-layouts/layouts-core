<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\UrlMapper;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use PHPUnit\Framework\TestCase;

class UrlMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\UrlMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new UrlMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\UrlMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(UrlType::class, $this->mapper->getFormType());
    }
}
