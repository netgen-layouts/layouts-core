<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Parameters;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class FormMapperPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_block_manager.parameters.registry.form_mapper', new Definition());

        $formMapper = new Definition();
        $formMapper->addTag(
            'netgen_block_manager.parameters.form.mapper',
            ['type' => 'test']
        );

        $this->setDefinition('netgen_block_manager.parameters.form.mapper.test', $formMapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.parameters.registry.form_mapper',
            'addFormMapper',
            [
                'test',
                new Reference('netgen_block_manager.parameters.form.mapper.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Parameter form mapper service definition must have a 'type' attribute in its' tag.
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType(): void
    {
        $this->setDefinition('netgen_block_manager.parameters.registry.form_mapper', new Definition());

        $formMapper = new Definition();
        $formMapper->addTag('netgen_block_manager.parameters.form.mapper');
        $this->setDefinition('netgen_block_manager.parameters.form.mapper.test', $formMapper);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\FormMapperPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FormMapperPass());
    }
}
