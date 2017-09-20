<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\Route;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RouteTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new Route();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\Route::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->mapper->getFormType());
    }
}
