<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Config;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ConfigDefinitionPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     */
    public function testProcess()
    {
        $configDefinitionHandler = new Definition();
        $configDefinitionHandler->addTag(
            'netgen_block_manager.config.config_definition_handler',
            array('identifier' => 'http_cache', 'type' => 'block')
        );

        $this->setDefinition(
            'netgen_block_manager.config.config_definition.handler.test',
            $configDefinitionHandler
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.config.config_definition.block.http_cache',
            ConfigDefinition::class
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Config definition handler definition must have a 'type' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagType()
    {
        $configDefinitionHandler = new Definition();
        $configDefinitionHandler->addTag('netgen_block_manager.config.config_definition_handler', array('identifier' => 'http_cache'));
        $this->setDefinition('netgen_block_manager.config.config_definition.handler.test', $configDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Config definition type "unknown" is not supported.
     */
    public function testProcessThrowsExceptionWithUnsupportedTagType()
    {
        $configDefinitionHandler = new Definition();
        $configDefinitionHandler->addTag('netgen_block_manager.config.config_definition_handler', array('type' => 'unknown', 'identifier' => 'http_cache'));
        $this->setDefinition('netgen_block_manager.config.config_definition.handler.test', $configDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Config definition handler definition must have an 'identifier' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $configDefinitionHandler = new Definition();
        $configDefinitionHandler->addTag('netgen_block_manager.config.config_definition_handler', array('type' => 'block'));
        $this->setDefinition('netgen_block_manager.config.config_definition.handler.test', $configDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Config\ConfigDefinitionPass::process
     * @doesNotPerformAssertions
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();
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
