<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class DefaultViewTemplatesPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::addDefaultRule
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::updateRules
     */
    public function testProcess(): void
    {
        $this->container->setParameter(
            'netgen_block_manager.view',
            [
                'test_view' => [
                    'api' => [
                        'override_match' => [
                            'template' => 'override_api.html.twig',
                        ],
                    ],
                ],
            ]
        );

        $this->container->setParameter(
            'netgen_block_manager.default_view_templates',
            [
                'test_view' => [
                    'default' => 'default.html.twig',
                    'api' => 'api.html.twig',
                ],
                'other_view' => [
                    'default' => 'default2.html.twig',
                    'api' => 'api2.html.twig',
                ],
            ]
        );

        $this->compile();

        self::assertContainerBuilderHasParameter(
            'netgen_block_manager.view',
            [
                'test_view' => [
                    'default' => [
                        '___test_view_default_default___' => [
                            'template' => 'default.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                    'api' => [
                        'override_match' => [
                            'template' => 'override_api.html.twig',
                        ],
                        '___test_view_api_default___' => [
                            'template' => 'api.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
                'other_view' => [
                    'default' => [
                        '___other_view_default_default___' => [
                            'template' => 'default2.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                    'api' => [
                        '___other_view_api_default___' => [
                            'template' => 'api2.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DefaultViewTemplatesPass());
    }
}
