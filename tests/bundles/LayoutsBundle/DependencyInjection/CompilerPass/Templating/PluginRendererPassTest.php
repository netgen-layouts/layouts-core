<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Templating;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Templating\PluginRendererPass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class PluginRendererPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new PluginRendererPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Templating\PluginRendererPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_layouts.templating.plugin_renderer', new Definition(null, [[], []]));

        $PluginRenderer1 = new Definition();
        $PluginRenderer1->addTag('netgen_layouts.template_plugin', ['plugin' => 'test1']);
        $this->setDefinition('netgen_layouts.templating.plugin.test1', $PluginRenderer1);

        $PluginRenderer2 = new Definition();
        $PluginRenderer2->addTag('netgen_layouts.template_plugin', ['plugin' => 'test2']);
        $this->setDefinition('netgen_layouts.templating.plugin.test2', $PluginRenderer2);

        $PluginRenderer3 = new Definition();
        $PluginRenderer3->addTag('netgen_layouts.template_plugin', ['plugin' => 'test2', 'priority' => 10]);
        $this->setDefinition('netgen_layouts.templating.plugin.test3', $PluginRenderer3);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.templating.plugin_renderer',
            1,
            [
                'test1' => [
                    new Reference('netgen_layouts.templating.plugin.test1'),
                ],
                'test2' => [
                    new Reference('netgen_layouts.templating.plugin.test3'),
                    new Reference('netgen_layouts.templating.plugin.test2'),
                ],
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Templating\PluginRendererPass::process
     */
    public function testProcessWithNoPluginNameInTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Template plugin service definition must have an \'plugin\' attribute in its\' tag.');

        $this->setDefinition('netgen_layouts.templating.plugin_renderer', new Definition());

        $PluginRenderer1 = new Definition();
        $PluginRenderer1->addTag('netgen_layouts.template_plugin');
        $this->setDefinition('netgen_layouts.templating.plugin.test1', $PluginRenderer1);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Templating\PluginRendererPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
