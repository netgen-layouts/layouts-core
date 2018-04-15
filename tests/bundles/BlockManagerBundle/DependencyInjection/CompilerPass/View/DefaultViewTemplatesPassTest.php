<?php

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
    public function testProcess()
    {
        $this->container->setParameter(
            'netgen_block_manager.view',
            array(
                'test_view' => array(
                    'api' => array(
                        'override_match' => array(
                            'template' => 'override_api.html.twig',
                        ),
                    ),
                ),
            )
        );

        $this->container->setParameter(
            'netgen_block_manager.default_view_templates',
            array(
                'test_view' => array(
                    'default' => 'default.html.twig',
                    'api' => 'api.html.twig',
                ),
                'other_view' => array(
                    'default' => 'default2.html.twig',
                    'api' => 'api2.html.twig',
                ),
            )
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.view',
            array(
                'test_view' => array(
                    'default' => array(
                        '___test_view_default_default___' => array(
                            'template' => 'default.html.twig',
                            'match' => array(),
                            'parameters' => array(),
                        ),
                    ),
                    'api' => array(
                        'override_match' => array(
                            'template' => 'override_api.html.twig',
                        ),
                        '___test_view_api_default___' => array(
                            'template' => 'api.html.twig',
                            'match' => array(),
                            'parameters' => array(),
                        ),
                    ),
                ),
                'other_view' => array(
                    'default' => array(
                        '___other_view_default_default___' => array(
                            'template' => 'default2.html.twig',
                            'match' => array(),
                            'parameters' => array(),
                        ),
                    ),
                    'api' => array(
                        '___other_view_api_default___' => array(
                            'template' => 'api2.html.twig',
                            'match' => array(),
                            'parameters' => array(),
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\DefaultViewTemplatesPass::process
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
        $container->addCompilerPass(new DefaultViewTemplatesPass());
    }
}
