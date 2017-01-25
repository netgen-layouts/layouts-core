<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TemplateResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     */
    public function testProcess()
    {
        $templateResolver = new Definition();
        $templateResolver->addArgument(array());
        $this->setDefinition('netgen_block_manager.view.template_resolver', $templateResolver);

        $matcher = new Definition();
        $matcher->addTag('netgen_block_manager.view.template_matcher', array('identifier' => 'block_type'));
        $this->setDefinition('netgen_block_manager.view.template_matcher.test', $matcher);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.template_resolver',
            0,
            array(
                'block_type' => new Reference('netgen_block_manager.view.template_matcher.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $templateResolver = new Definition();
        $templateResolver->addArgument(array());
        $this->setDefinition('netgen_block_manager.view.template_resolver', $templateResolver);

        $matcher = new Definition();
        $matcher->addTag('netgen_block_manager.view.template_matcher');
        $this->setDefinition('netgen_block_manager.view.template_matcher.test', $matcher);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
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
        $container->addCompilerPass(new TemplateResolverPass());
    }
}