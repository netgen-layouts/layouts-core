<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Container;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\BlockManager\Layout\Container\ContainerDefinition;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandler;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandler;
use Netgen\BlockManager\Layout\Container\DynamicContainerDefinition;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ContainerDefinitionPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @param string $handlerClass
     * @param string $definitionClass
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass::process
     * @dataProvider processDataProvider
     */
    public function testProcess($handlerClass, $definitionClass)
    {
        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array('container_definition' => array('enabled' => true))
        );

        $this->setDefinition('netgen_block_manager.container.registry.container_definition', new Definition());

        $containerDefinitionHandler = new Definition($handlerClass);
        $containerDefinitionHandler->addTag(
            'netgen_block_manager.container.container_definition_handler',
            array('identifier' => 'container_definition')
        );

        $this->setDefinition(
            'netgen_block_manager.container.container_definition.handler.test',
            $containerDefinitionHandler
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.container.container_definition.container_definition',
            $definitionClass
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.container.registry.container_definition',
            'addContainerDefinition',
            array(
                'container_definition',
                new Reference('netgen_block_manager.container.container_definition.container_definition'),
            )
        );
    }

    /**
     * @param string $handlerClass
     * @param string $definitionClass
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass::process
     * @dataProvider processDataProvider
     */
    public function testProcessWithCustomHandler($handlerClass, $definitionClass)
    {
        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array('container_definition' => array('enabled' => true, 'handler' => 'custom'))
        );

        $this->setDefinition('netgen_block_manager.container.registry.container_definition', new Definition());

        $containerDefinitionHandler = new Definition($handlerClass);
        $containerDefinitionHandler->addTag(
            'netgen_block_manager.container.container_definition_handler',
            array('identifier' => 'custom')
        );

        $this->setDefinition(
            'netgen_block_manager.container.container_definition.handler.test',
            $containerDefinitionHandler
        );

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.container.container_definition.container_definition',
            $definitionClass
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.container.registry.container_definition',
            'addContainerDefinition',
            array(
                'container_definition',
                new Reference('netgen_block_manager.container.container_definition.container_definition'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass::process
     */
    public function testProcessWithDisabledContainerDefinition()
    {
        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array('container_definition' => array('enabled' => false))
        );

        $this->setDefinition('netgen_block_manager.container.registry.container_definition', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.container.container_definition.container_definition');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier()
    {
        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array('container_definition' => array('enabled' => true))
        );

        $this->setDefinition('netgen_block_manager.container.registry.container_definition', new Definition());

        $containerDefinitionHandler = new Definition();
        $containerDefinitionHandler->addTag('netgen_block_manager.container.container_definition_handler');
        $this->setDefinition('netgen_block_manager.container.container_definition.handler.test', $containerDefinitionHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoHandler()
    {
        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array('container_definition' => array('enabled' => true))
        );

        $this->setDefinition('netgen_block_manager.container.registry.container_definition', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoCustomHandler()
    {
        $this->setParameter(
            'netgen_block_manager.container_definitions',
            array('container_definition' => array('enabled' => true, 'handler' => 'custom'))
        );

        $this->setDefinition('netgen_block_manager.container.registry.container_definition', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Container\ContainerDefinitionPass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }

    public function processDataProvider()
    {
        return array(
            array(ContainerDefinitionHandler::class, ContainerDefinition::class),
            array(DynamicContainerDefinitionHandler::class, DynamicContainerDefinition::class),
        );
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ContainerDefinitionPass());
    }
}
