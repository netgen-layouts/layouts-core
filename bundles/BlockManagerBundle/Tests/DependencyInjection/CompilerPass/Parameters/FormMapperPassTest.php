<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FormMapperPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormMapperPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.parameters.form_mapper', new Definition());

        $parameterHandler = new Definition();
        $parameterHandler->addTag(
            'netgen_block_manager.parameters.parameter_handler',
            array('type' => 'test')
        );
        $this->setDefinition('netgen_block_manager.parameters.parameter_handler.test', $parameterHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.parameters.form_mapper',
            'addParameterHandler',
            array(
                'test',
                new Reference('netgen_block_manager.parameters.parameter_handler.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $this->setDefinition('netgen_block_manager.parameters.form_mapper', new Definition());

        $parameterHandler = new Definition();
        $parameterHandler->addTag('netgen_block_manager.parameters.parameter_handler');
        $this->setDefinition('netgen_block_manager.parameters.parameter_handler.test', $parameterHandler);

        $this->compile();
    }
}
