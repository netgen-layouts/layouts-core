<?php

namespace Netgen\BlockManager\Tests\Parameters\Registry;

use ArrayIterator;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use PHPUnit\Framework\TestCase;

final class FormMapperRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    private $formMapper;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new FormMapperRegistry();

        $this->formMapper = new FormMapper();

        $this->registry->addFormMapper('mapper', $this->formMapper);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::addFormMapper
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::getFormMappers
     */
    public function testAddFormMapper()
    {
        $this->assertEquals(['mapper' => $this->formMapper], $this->registry->getFormMappers());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::hasFormMapper
     */
    public function testHasFormMapper()
    {
        $this->assertTrue($this->registry->hasFormMapper('mapper'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::hasFormMapper
     */
    public function testHasFormMapperWithNoFormMapper()
    {
        $this->assertFalse($this->registry->hasFormMapper('other_mapper'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::getFormMapper
     */
    public function testGetFormMapper()
    {
        $this->assertEquals($this->formMapper, $this->registry->getFormMapper('mapper'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::getFormMapper
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     * @expectedExceptionMessage Form mapper for "other_mapper" parameter type does not exist.
     */
    public function testGetFormMapperThrowsParameterTypeException()
    {
        $this->registry->getFormMapper('other_mapper');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $formMappers = [];
        foreach ($this->registry as $identifier => $formMapper) {
            $formMappers[$identifier] = $formMapper;
        }

        $this->assertEquals($this->registry->getFormMappers(), $formMappers);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::count
     */
    public function testCount()
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('mapper', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals($this->formMapper, $this->registry['mapper']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet()
    {
        $this->registry['mapper'] = $this->formMapper;
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset()
    {
        unset($this->registry['mapper']);
    }
}
