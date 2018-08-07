<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Twig;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig\RuntimeLoaderPass;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class RuntimeLoaderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig\RuntimeLoaderPass::process
     */
    public function testProcess(): void
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
            [stdClass::class, 'netgen_block_manager.twig.runtime.test']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'twig',
            'addRuntimeLoader',
            [new Reference('netgen_block_manager.templating.twig.runtime.container_loader')]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig\RuntimeLoaderPass::process
     */
    public function testProcessWithExistingContainerLoader(): void
    {
        $this->setDefinition('twig', new Definition());
        $this->setDefinition('twig.runtime_loader', new Definition());
        $this->setDefinition('netgen_block_manager.templating.twig.runtime.container_loader', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasServiceDefinitionWithMethodCall(
            'twig',
            'addRuntimeLoader',
            [new Reference('netgen_block_manager.templating.twig.runtime.container_loader')]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Twig\RuntimeLoaderPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RuntimeLoaderPass());
    }

    /**
     * Assert that the ContainerBuilder for this test has a service definition with the given id,
     * which does not have a method call to the given method with the given arguments.
     */
    private function assertContainerBuilderNotHasServiceDefinitionWithMethodCall(
        string $serviceId,
        string $method,
        array $arguments = [],
        ?int $index = null
    ): void {
        self::assertThat(
            $this->container->findDefinition($serviceId),
            self::logicalNot(
                new DefinitionHasMethodCallConstraint(
                    $method,
                    $arguments,
                    $index
                )
            )
        );
    }
}
