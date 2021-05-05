<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class DefaultViewTemplatesPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new DefaultViewTemplatesPass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::addDefaultRule
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::process
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::updateRules
     */
    public function testProcess(): void
    {
        $this->container->setParameter(
            'netgen_layouts.view',
            [
                'test_view' => [
                    'app' => [
                        'override_match' => [
                            'template' => 'override_app.html.twig',
                        ],
                    ],
                ],
            ],
        );

        $this->container->setParameter(
            'netgen_layouts.default_view_templates',
            [
                'test_view' => [
                    'default' => 'default.html.twig',
                    'app' => 'app.html.twig',
                ],
                'other_view' => [
                    'default' => 'default2.html.twig',
                    'app' => 'app2.html.twig',
                ],
            ],
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'netgen_layouts.view',
            [
                'test_view' => [
                    'default' => [
                        '___test_view_default_default___' => [
                            'template' => 'default.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                    'app' => [
                        'override_match' => [
                            'template' => 'override_app.html.twig',
                        ],
                        '___test_view_app_default___' => [
                            'template' => 'app.html.twig',
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
                    'app' => [
                        '___other_view_app_default___' => [
                            'template' => 'app2.html.twig',
                            'match' => [],
                            'parameters' => [],
                        ],
                    ],
                ],
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
