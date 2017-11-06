<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Design;

use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('twig.loader.native_filesystem')) {
            return;
        }

        $twigLoader = $container->getDefinition('twig.loader.native_filesystem');

        // Reversing the list of bundles so bundles added at end have higher priority
        // when searching for a template
        $bundles = array_reverse($container->getParameter('kernel.bundles_metadata'));
        $designList = $container->getParameter('netgen_block_manager.design_list');
        $themeList = array_unique(array_merge(...array_values($designList)));

        $themeDirs = array();

        $this->populateThemeDirs($container, $themeList, $this->getAppDir($container) . '/Resources/views/ngbm/themes', $themeDirs);

        foreach ($bundles as $bundleName => $bundleMetadata) {
            $this->populateThemeDirs($container, $themeList, $bundleMetadata['path'] . '/Resources/views/ngbm/themes', $themeDirs);
        }

        foreach ($designList as $designName => $designThemes) {
            foreach ($designThemes as $themeName) {
                foreach ($themeDirs[$themeName] as $themeDir => $themeDirExists) {
                    if ($themeDirExists) {
                        $twigLoader->addMethodCall('addPath', array($themeDir, 'ngbm_' . $designName));
                    }
                }
            }
        }
    }

    /**
     * Fills the $themeDirs with all found paths for provided theme list and path.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $themeList
     * @param string $path
     * @param array $themeDirs
     */
    private function populateThemeDirs(ContainerBuilder $container, array $themeList, $path, array &$themeDirs)
    {
        if (!is_dir($path)) {
            return;
        }

        foreach ($themeList as $themeName) {
            $themeDir = $path . '/' . $themeName;
            $themeDirs[$themeName][$themeDir] = is_dir($themeDir);

            $container->addResource(new FileExistenceResource($themeDir));
        }
    }

    /**
     * Returns the current app dir, abstracting Symfony 3.3+, where kernel.project_dir is available,
     * and Symfony 2.8 support, where only kernel.root_dir exists.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return string
     */
    private function getAppDir(ContainerBuilder $container)
    {
        if ($container->hasParameter('kernel.project_dir')) {
            return $container->getParameter('kernel.project_dir') . '/' . $container->getParameter('kernel.name');
        }

        return $container->getParameter('kernel.root_dir');
    }
}
