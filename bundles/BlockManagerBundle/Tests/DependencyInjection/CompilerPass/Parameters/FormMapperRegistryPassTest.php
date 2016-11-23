<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Parameters;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FormMapperRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormMapperRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperRegistryPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.parameters.registry.form_mapper', new Definition());

        $formMapper = new Definition();
        $formMapper->addTag(
            'netgen_block_manager.parameters.form.mapper',
            array('type' => 'test')
        );

        $this->setDefinition('netgen_block_manager.parameters.form.mapper.test', $formMapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.parameters.registry.form_mapper',
            'addFormMapper',
            array(
                'test',
                new Reference('netgen_block_manager.parameters.form.mapper.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $this->setDefinition('netgen_block_manager.parameters.registry.form_mapper', new Definition());

        $formMapper = new Definition();
        $formMapper->addTag('netgen_block_manager.parameters.form.mapper');
        $this->setDefinition('netgen_block_manager.parameters.form.mapper.test', $formMapper);

        $this->compile();
    }
}
