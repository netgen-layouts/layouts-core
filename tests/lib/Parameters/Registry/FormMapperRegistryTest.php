<?php

namespace Netgen\BlockManager\Tests\Parameters\Registry;

use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use PHPUnit\Framework\TestCase;

class FormMapperRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    protected $formMapper;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\FormMapperRegistry
     */
    protected $registry;

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
        $this->assertEquals(array('mapper' => $this->formMapper), $this->registry->getFormMappers());
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
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetFormMapperThrowsInvalidArgumentException()
    {
        $this->registry->getFormMapper('other_mapper');
    }
}
