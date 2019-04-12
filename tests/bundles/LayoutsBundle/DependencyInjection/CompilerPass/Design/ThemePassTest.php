<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Design;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Design\ThemePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

final class ThemePassTest extends AbstractCompilerPassTestCase
{
    public function setUp(): void
    {
        @mkdir('/tmp/nglayouts/templates/ngbm/themes/theme2', 0777, true);
        @mkdir('/tmp/nglayouts/templates/ngbm/themes/theme3', 0777, true);
        @mkdir('/tmp/nglayouts/app/Resources/views/ngbm/themes/theme3', 0777, true);
        @mkdir('/tmp/nglayouts/app/Resources/views/ngbm/themes/standard', 0777, true);
        @mkdir('/tmp/nglayouts/bundles/first/Resources/views/ngbm/themes/theme1', 0777, true);
        @mkdir('/tmp/nglayouts/bundles/first/Resources/views/ngbm/themes/theme3', 0777, true);
        @mkdir('/tmp/nglayouts/bundles/second/Resources/views/ngbm/themes/theme1', 0777, true);
        @mkdir('/tmp/nglayouts/bundles/second/Resources/views/ngbm/themes/theme2', 0777, true);
        @mkdir('/tmp/nglayouts/bundles/second/Resources/views/ngbm/themes/standard', 0777, true);

        parent::setUp();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Design\ThemePass::getAppDir
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Design\ThemePass::getThemeDirs
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Design\ThemePass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('twig.loader.native_filesystem', new Definition());

        $designList = [
            'design1' => ['theme1', 'theme2'],
            'design2' => ['theme2', 'theme3'],
        ];

        $this->setParameter(
            'kernel.bundles_metadata',
            [
                'App\First' => ['path' => '/tmp/nglayouts/bundles/first'],
                'App\Second' => ['path' => '/tmp/nglayouts/bundles/second'],
            ]
        );

        $this->setParameter('kernel.project_dir', '/tmp/nglayouts');
        $this->setParameter('twig.default_path', '/tmp/nglayouts/templates');
        $this->setParameter('kernel.name', 'app');

        $this->setParameter('netgen_layouts.design_list', $designList);

        $this->compile();

        $designList['design1'][] = 'standard';
        $designList['design2'][] = 'standard';

        $themeDirs = [
            'theme1' => [
                '/tmp/nglayouts/bundles/second/Resources/views/ngbm/themes/theme1',
                '/tmp/nglayouts/bundles/first/Resources/views/ngbm/themes/theme1',
            ],
            'theme2' => [
                '/tmp/nglayouts/templates/ngbm/themes/theme2',
                '/tmp/nglayouts/bundles/second/Resources/views/ngbm/themes/theme2',
            ],
            'theme3' => [
                '/tmp/nglayouts/app/Resources/views/ngbm/themes/theme3',
                '/tmp/nglayouts/templates/ngbm/themes/theme3',
                '/tmp/nglayouts/bundles/first/Resources/views/ngbm/themes/theme3',
            ],
            'standard' => [
                '/tmp/nglayouts/app/Resources/views/ngbm/themes/standard',
                '/tmp/nglayouts/bundles/second/Resources/views/ngbm/themes/standard',
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
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Design\ThemePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ThemePass());
    }
}
