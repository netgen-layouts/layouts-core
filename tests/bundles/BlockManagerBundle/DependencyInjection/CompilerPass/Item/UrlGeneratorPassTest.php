<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Item;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlGeneratorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class UrlGeneratorPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlGeneratorPass::process
     */
    public function testProcess(): void
    {
        $urlGenerator = new Definition();
        $urlGenerator->addArgument(null);

        $this->setDefinition('netgen_block_manager.item.url_generator', $urlGenerator);

        $valueUrlGenerator = new Definition();
        $valueUrlGenerator->addTag('netgen_block_manager.item.value_url_generator', ['value_type' => 'test']);
        $this->setDefinition('netgen_block_manager.item.value_url_generator.test', $valueUrlGenerator);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.item.url_generator',
            0,
            [
                'test' => new Reference('netgen_block_manager.item.value_url_generator.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlGeneratorPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidValueTypeTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value type must begin with a letter and be followed by any combination of letters, digits and underscore.');

        $this->setDefinition('netgen_block_manager.item.url_generator', new Definition());

        $valueUrlGenerator = new Definition();
        $valueUrlGenerator->addTag('netgen_block_manager.item.value_url_generator', ['value_type' => '123']);
        $this->setDefinition('netgen_block_manager.item.value_url_generator.test', $valueUrlGenerator);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlGeneratorPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagValueType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Value URL generator service definition must have a \'value_type\' attribute in its\' tag.');

        $urlGenerator = new Definition();
        $urlGenerator->addArgument(null);

        $this->setDefinition('netgen_block_manager.item.url_generator', $urlGenerator);

        $valueUrlGenerator = new Definition();
        $valueUrlGenerator->addTag('netgen_block_manager.item.value_url_generator');
        $this->setDefinition('netgen_block_manager.item.value_url_generator.test', $valueUrlGenerator);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item\UrlGeneratorPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new UrlGeneratorPass());
    }
}
