<?php

namespace Netgen\BlockManager\Tests\Layout\Container\ContainerDefinition\Configuration;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

class ViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType
     */
    protected $viewType;

    public function setUp()
    {
        $this->viewType = new ViewType(
            array(
                'identifier' => 'large',
                'name' => 'Large',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType::__construct
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('large', $this->viewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Large', $this->viewType->getName());
    }
}
