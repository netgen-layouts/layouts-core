<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PHPUnit\Framework\TestCase;

class PathInfoPrefixTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new PathInfoPrefix();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->mapper->getFormType());
    }
}
