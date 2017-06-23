<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Config;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

class ConfigDefinitionPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     */
    public function testProcess()
    {
        $configDefinitionHandler = new Definition();
        $configDefinitionHandler->addTag(
            'netgen_block_manager.block.config_definition_handler',
            array('config_key' => 'http_cache')
        );

        $this->setDefinition(
            'netgen_block_manager.block.config_definition.handler.test',
            $configDefinitionHandler
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.block.config_definition.http_cache',
            ConfigDefinition::class
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Config definition handler definition must have an 'config_key' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagConfigKey()
    {
        $configDefinitionHandler = new Definition();
        $configDefinitionHandler->addTag('netgen_block_manager.block.config_definition_handler');
        $this->setDefinition('netgen_block_manager.block.config_definition.handler.test', $configDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Config definition with 'http_cache' config key is defined more than once for 'block' config type.
     */
    public function testProcessThrowsExceptionWithDuplicateConfigKeys()
    {
        $configDefinitionHandler = new Definition();
        $configDefinitionHandler->addTag(
            'netgen_block_manager.block.config_definition_handler',
            array('config_key' => 'http_cache')
        );

        $this->setDefinition(
            'netgen_block_manager.block.config_definition.handler.test',
            $configDefinitionHandler
        );

        $configDefinitionHandler2 = new Definition();
        $configDefinitionHandler2->addTag(
            'netgen_block_manager.block.config_definition_handler',
            array('config_key' => 'http_cache')
        );

        $this->setDefinition(
            'netgen_block_manager.block.config_definition.handler.test2',
            $configDefinitionHandler2
        );

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
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
        $container->addCompilerPass(new ConfigDefinitionPass());
    }
}
