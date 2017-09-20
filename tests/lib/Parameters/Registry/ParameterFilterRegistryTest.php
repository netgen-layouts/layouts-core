<?php

namespace Netgen\BlockManager\Tests\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use PHPUnit\Framework\TestCase;

class ParameterFilterRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterFilterInterface
     */
    private $filter;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new ParameterFilterRegistry();

        $this->filter = $this->createMock(ParameterFilterInterface::class);
        $this->registry->addParameterFilter('html', $this->filter);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry::addParameterFilter
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry::getParameterFilters
     */
    public function testGetParameterFilters()
    {
        $this->assertEquals(array($this->filter), $this->registry->getParameterFilters('html'));
    }
}
