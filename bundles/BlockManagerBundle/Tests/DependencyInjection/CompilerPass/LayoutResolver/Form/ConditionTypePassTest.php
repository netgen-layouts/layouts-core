<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConditionTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConditionTypePass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass::process
     */
    public function testProcess()
    {
        $formType = new Definition();
        $formType->addArgument(array());
        $this->setDefinition('netgen_block_manager.layout.resolver.form.condition_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_block_manager.layout.resolver.form.condition_type.mapper', array('identifier' => 'condition'));
        $this->setDefinition('netgen_block_manager.layout.resolver.form.condition_type.mapper.test', $mapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.form.condition_type',
            0,
            array(
                'condition' => new Reference('netgen_block_manager.layout.resolver.form.condition_type.mapper.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\ConditionTypePass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $formType = new Definition();
        $formType->addArgument(array());
        $this->setDefinition('netgen_block_manager.layout.resolver.form.condition_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_block_manager.layout.resolver.form.condition_type.mapper');
        $this->setDefinition('netgen_block_manager.layout.resolver.form.condition_type.mapper.test', $mapper);

        $this->compile();
    }
}
