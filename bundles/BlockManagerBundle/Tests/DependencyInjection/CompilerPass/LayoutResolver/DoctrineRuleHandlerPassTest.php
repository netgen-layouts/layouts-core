<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineRuleHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineRuleHandlerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DoctrineRuleHandlerPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineRuleHandlerPass::process
     */
    public function testProcess()
    {
        $ruleHandler = new Definition();
        $this->setDefinition('netgen_block_manager.layout_resolver.rule_handler.doctrine.handler', $ruleHandler);

        $targetHandler = new Definition();
        $targetHandler->addTag(
            'netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler',
            array(
                'alias' => 'test',
            )
        );
        $this->setDefinition('netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler.test', $targetHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.layout_resolver.rule_handler.doctrine.handler',
            'addTargetHandler',
            array(
                'test', new Reference('netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineRuleHandlerPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWhenNoAlias()
    {
        $ruleHandler = new Definition();
        $this->setDefinition('netgen_block_manager.layout_resolver.rule_handler.doctrine.handler', $ruleHandler);

        $targetHandler = new Definition();
        $targetHandler->addTag('netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler');
        $this->setDefinition('netgen_block_manager.layout_resolver.rule_handler.doctrine.target_handler.test', $targetHandler);

        $this->compile();
    }
}
