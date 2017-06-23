<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Twig;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig\RuntimeLoaderPass;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class RuntimeLoaderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig\RuntimeLoaderPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('twig', new Definition());
        $this->setDefinition('netgen_block_manager.templating.twig.runtime.container_loader', new Definition());

        $runtime = new Definition(stdClass::class);
        $runtime->addTag('netgen_block_manager.twig.runtime');
        $this->setDefinition('netgen_block_manager.twig.runtime.test', $runtime);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.templating.twig.runtime.container_loader',
            'addRuntime',
            array(stdClass::class, 'netgen_block_manager.twig.runtime.test')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'twig',
            'addRuntimeLoader',
            array(new Reference('netgen_block_manager.templating.twig.runtime.container_loader'))
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig\RuntimeLoaderPass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RuntimeLoaderPass());
    }
}
