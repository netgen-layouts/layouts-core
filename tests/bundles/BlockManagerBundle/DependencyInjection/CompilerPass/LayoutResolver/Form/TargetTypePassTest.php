<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver\Form;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TargetTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     */
    public function testProcess()
    {
        $formType = new Definition();
        $formType->addArgument(array());
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_block_manager.layout.resolver.form.target_type.mapper', array('target_type' => 'target'));
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type.mapper.test', $mapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.layout.resolver.form.target_type',
            0,
            array(
                'target' => new Reference('netgen_block_manager.layout.resolver.form.target_type.mapper.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $formType = new Definition();
        $formType->addArgument(array());
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type', $formType);

        $mapper = new Definition();
        $mapper->addTag('netgen_block_manager.layout.resolver.form.target_type.mapper');
        $this->setDefinition('netgen_block_manager.layout.resolver.form.target_type.mapper.test', $mapper);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\Form\TargetTypePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TargetTypePass());
    }
}
