<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Locale;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Locale\ChainedLocaleContextPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class ChainedLocaleContextPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Locale\ChainedLocaleContextPass::process
     */
    public function testProcess()
    {
        $chainedLocaleContext = new Definition();
        $chainedLocaleContext->addArgument(array());
        $this->setDefinition('netgen_block_manager.locale.context.chained', $chainedLocaleContext);

        $localeContext1 = new Definition();
        $localeContext1->addTag('netgen_block_manager.locale.context');
        $this->setDefinition('netgen_block_manager.locale.context.test1', $localeContext1);

        $localeContext2 = new Definition();
        $localeContext2->addTag('netgen_block_manager.locale.context', array('priority' => 5));
        $this->setDefinition('netgen_block_manager.locale.context.test2', $localeContext2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.locale.context.chained',
            0,
            array(
                new Reference('netgen_block_manager.locale.context.test2'),
                new Reference('netgen_block_manager.locale.context.test1'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Locale\ChainedLocaleContextPass::process
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
        $container->addCompilerPass(new ChainedLocaleContextPass());
    }
}
