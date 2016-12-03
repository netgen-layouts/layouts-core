<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RequestUri;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class RequestUriTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new RequestUri();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RequestUri::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->mapper->getFormType());
    }
}
