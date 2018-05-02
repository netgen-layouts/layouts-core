<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Design;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class ThemePassTest extends AbstractCompilerPassTestCase
{
    public function setUp()
    {
        @mkdir('/tmp/ngbm/templates/ngbm/themes/theme2', 0777, true);
        @mkdir('/tmp/ngbm/templates/ngbm/themes/theme3', 0777, true);
        @mkdir('/tmp/ngbm/app/Resources/views/ngbm/themes/theme3', 0777, true);
        @mkdir('/tmp/ngbm/app/Resources/views/ngbm/themes/standard', 0777, true);
        @mkdir('/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme1', 0777, true);
        @mkdir('/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme3', 0777, true);
        @mkdir('/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme1', 0777, true);
        @mkdir('/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme2', 0777, true);
        @mkdir('/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/standard', 0777, true);

        parent::setUp();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::getAppDir
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::getThemeDirs
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::process
     */
    public function testProcess()
    {
        $this->setDefinition('twig.loader.native_filesystem', new Definition());

        $designList = [
            'design1' => ['theme1', 'theme2'],
            'design2' => ['theme2', 'theme3'],
        ];

        $this->setParameter(
            'kernel.bundles_metadata',
            [
                'App\First' => ['path' => '/tmp/ngbm/bundles/first'],
                'App\Second' => ['path' => '/tmp/ngbm/bundles/second'],
            ]
        );

        $this->setParameter('kernel.project_dir', '/tmp/ngbm');
        $this->setParameter('twig.default_path', '/tmp/ngbm/templates');
        $this->setParameter('kernel.name', 'app');

        $this->setParameter('netgen_block_manager.design_list', $designList);

        $this->compile();

        $designList['design1'][] = 'standard';
        $designList['design2'][] = 'standard';

        $themeDirs = [
            'theme1' => [
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme1',
                '/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme1',
            ],
            'theme2' => [
                '/tmp/ngbm/templates/ngbm/themes/theme2',
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme2',
            ],
            'theme3' => [
                '/tmp/ngbm/app/Resources/views/ngbm/themes/theme3',
                '/tmp/ngbm/templates/ngbm/themes/theme3',
                '/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme3',
            ],
            'standard' => [
                '/tmp/ngbm/app/Resources/views/ngbm/themes/standard',
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/standard',
            ],
        ];

        $index = -1;
        foreach ($designList as $designName => $themes) {
            foreach ($themes as $theme) {
                foreach ($themeDirs[$theme] as $themeDir) {
                    $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
                        'twig.loader.native_filesystem',
                        'addPath',
                        [$themeDir, 'ngbm_' . $designName],
                        ++$index
                    );
                }
            }
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::getAppDir
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::getThemeDirs
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::process
     */
    public function testProcessWithoutTwigDefaultPath()
    {
        $this->setDefinition('twig.loader.native_filesystem', new Definition());

        $designList = [
            'design1' => ['theme1', 'theme2'],
            'design2' => ['theme2', 'theme3'],
        ];

        $this->setParameter(
            'kernel.bundles_metadata',
            [
                'App\First' => ['path' => '/tmp/ngbm/bundles/first'],
                'App\Second' => ['path' => '/tmp/ngbm/bundles/second'],
            ]
        );

        $this->setParameter('kernel.project_dir', '/tmp/ngbm');
        $this->setParameter('kernel.name', 'app');

        $this->setParameter('netgen_block_manager.design_list', $designList);

        $this->compile();

        $designList['design1'][] = 'standard';
        $designList['design2'][] = 'standard';

        $themeDirs = [
            'theme1' => [
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme1',
                '/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme1',
            ],
            'theme2' => [
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme2',
            ],
            'theme3' => [
                '/tmp/ngbm/app/Resources/views/ngbm/themes/theme3',
                '/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme3',
            ],
            'standard' => [
                '/tmp/ngbm/app/Resources/views/ngbm/themes/standard',
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/standard',
            ],
        ];

        $index = -1;
        foreach ($designList as $designName => $themes) {
            foreach ($themes as $theme) {
                foreach ($themeDirs[$theme] as $themeDir) {
                    $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
                        'twig.loader.native_filesystem',
                        'addPath',
                        [$themeDir, 'ngbm_' . $designName],
                        ++$index
                    );
                }
            }
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::getAppDir
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::getThemeDirs
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::process
     */
    public function testProcessWithRootDir()
    {
        $this->setDefinition('twig.loader.native_filesystem', new Definition());

        $designList = [
            'design1' => ['theme1', 'theme2'],
            'design2' => ['theme2', 'theme3'],
        ];

        $this->setParameter(
            'kernel.bundles_metadata',
            [
                'App\First' => ['path' => '/tmp/ngbm/bundles/first'],
                'App\Second' => ['path' => '/tmp/ngbm/bundles/second'],
            ]
        );

        $this->setParameter('kernel.root_dir', '/tmp/ngbm/app');
        $this->setParameter('kernel.name', 'app');

        $this->setParameter('netgen_block_manager.design_list', $designList);

        $this->compile();

        $designList['design1'][] = 'standard';
        $designList['design2'][] = 'standard';

        $themeDirs = [
            'theme1' => [
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme1',
                '/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme1',
            ],
            'theme2' => [
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/theme2',
            ],
            'theme3' => [
                '/tmp/ngbm/app/Resources/views/ngbm/themes/theme3',
                '/tmp/ngbm/bundles/first/Resources/views/ngbm/themes/theme3',
            ],
            'standard' => [
                '/tmp/ngbm/app/Resources/views/ngbm/themes/standard',
                '/tmp/ngbm/bundles/second/Resources/views/ngbm/themes/standard',
            ],
        ];

        $index = -1;
        foreach ($designList as $designName => $themes) {
            foreach ($themes as $theme) {
                foreach ($themeDirs[$theme] as $themeDir) {
                    $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
                        'twig.loader.native_filesystem',
                        'addPath',
                        [$themeDir, 'ngbm_' . $designName],
                        ++$index
                    );
                }
            }
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design\ThemePass::process
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
        $container->addCompilerPass(new ThemePass());
    }
}
