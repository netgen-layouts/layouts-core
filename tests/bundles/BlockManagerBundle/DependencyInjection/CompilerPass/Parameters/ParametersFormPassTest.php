<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Parameters;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ParametersFormPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_block_manager.parameters.form.parameters', new Definition(null, [[]]));

        $formMapper = new Definition();
        $formMapper->addTag(
            'netgen_block_manager.parameters.form.mapper',
            ['type' => 'test']
        );

        $this->setDefinition('netgen_block_manager.parameters.form.mapper.test', $formMapper);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.parameters.form.parameters',
            0,
            [
                'test' => new Reference('netgen_block_manager.parameters.form.mapper.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Parameter form mapper service definition must have a 'type' attribute in its' tag.
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType(): void
    {
        $this->setDefinition('netgen_block_manager.parameters.form.parameters', new Definition());

        $formMapper = new Definition();
        $formMapper->addTag('netgen_block_manager.parameters.form.mapper');
        $this->setDefinition('netgen_block_manager.parameters.form.mapper.test', $formMapper);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters\ParametersFormPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ParametersFormPass());
    }
}
