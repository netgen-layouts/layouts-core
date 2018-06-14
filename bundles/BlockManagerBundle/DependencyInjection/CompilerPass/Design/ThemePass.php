<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design;

use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('twig.loader.native_filesystem')) {
            return;
        }

        $twigLoader = $container->getDefinition('twig.loader.native_filesystem');

        $designList = $container->getParameter('netgen_block_manager.design_list');
        $themeList = array_unique(array_merge(...array_values($designList)));
        $themeList[] = 'standard';

        $themeDirs = $this->getThemeDirs($container, $themeList);

        foreach ($designList as $designName => $designThemes) {
            $designThemes[] = 'standard';

            foreach ($designThemes as $themeName) {
                foreach ($themeDirs[$themeName] as $themeDir) {
                    $container->addResource(new FileExistenceResource($themeDir));

                    if (is_dir($themeDir)) {
                        $twigLoader->addMethodCall('addPath', [$themeDir, 'ngbm_' . $designName]);
                    }
                }
            }
        }
    }

    /**
     * Returns an array with all found paths for provided theme list.
     */
    private function getThemeDirs(ContainerBuilder $container, array $themeList): array
    {
        $paths = array_map(
            function (array $bundleMetadata): string {
                return $bundleMetadata['path'] . '/Resources/views/ngbm/themes';
            },
            // Reversing the list of bundles so bundles added at end have higher priority
            // when searching for a template
            array_reverse($container->getParameter('kernel.bundles_metadata'))
        );

        $paths = array_values(array_filter($paths, 'is_dir'));

        if ($container->hasParameter('twig.default_path')) {
            $defaultTwigDir = $container->getParameterBag()->resolveValue($container->getParameter('twig.default_path')) . '/ngbm/themes';
            if (is_dir($defaultTwigDir)) {
                array_unshift($paths, $defaultTwigDir);
            }
        }

        $appDir = $this->getAppDir($container) . '/Resources/views/ngbm/themes';
        if (is_dir($appDir)) {
            array_unshift($paths, $appDir);
        }

        $themeDirs = [];
        foreach ($paths as $path) {
            foreach ($themeList as $themeName) {
                $themeDirs[$themeName][] = $path . '/' . $themeName;
            }
        }

        return $themeDirs;
    }

    /**
     * Returns the current app dir, abstracting Symfony 3.3+, where kernel.project_dir is available,
     * and Symfony 2.8 support, where only kernel.root_dir exists.
     */
    private function getAppDir(ContainerBuilder $container): string
    {
        if ($container->hasParameter('kernel.project_dir')) {
            return (string) $container->getParameter('kernel.project_dir') . '/' . (string) $container->getParameter('kernel.name');
        }

        return (string) $container->getParameter('kernel.root_dir');
    }
}
