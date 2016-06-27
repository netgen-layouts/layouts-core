<?php

namespace Netgen\BlockManager\Tests\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use PHPUnit\Framework\TestCase;
use stdClass;

class ParameterFilterRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterFilterInterface
     */
    protected $filter;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ParameterFilterRegistry();

        $this->filter = $this->createMock(ParameterFilterInterface::class);
        $this->registry->addParameterFilters('html', array($this->filter));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry::addParameterFilters
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry::getParameterFilters
     */
    public function testGetParameterFilters()
    {
        self::assertEquals(array($this->filter), $this->registry->getParameterFilters('html'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry::addParameterFilters
     * @expectedException \RuntimeException
     */
    public function testAddParameterFiltersThrowRuntimeException()
    {
        $this->registry->addParameterFilters('type', array(new stdClass()));
    }
}
