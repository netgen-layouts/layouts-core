<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\Bundle\ContentBrowserUIBundle\Version as ContentBrowserUIVersion;
use Netgen\Bundle\BlockManagerUIBundle\Version as BlockManagerUIVersion;
use Netgen\BlockManager\Version as BlockManagerVersion;

class AssetPackagesPass implements CompilerPassInterface
{
    /**
     * @var array
     */
    protected $packages = array(
        'ngbm_admin_css' => array(
            'base_path' => '/bundles/netgenblockmanageradmin/css',
            'version' => BlockManagerVersion::VERSION_ID,
            'version_format' => 'v%%2$s/%%1$s',
        ),
        'ngbm_admin_js' => array(
            'base_path' => '/bundles/netgenblockmanageradmin/js',
            'version' => BlockManagerVersion::VERSION_ID,
            'version_format' => 'v%%2$s/%%1$s',
        ),
        'ngbm_admin_vendor' => array(
            'base_path' => '/bundles/netgenblockmanageradmin/vendor',
            'version' => null,
            'version_format' => null,
        ),
        'ngbm_app_css' => array(
            'base_path' => '/bundles/netgenblockmanagerui/css',
            'version' => BlockManagerUIVersion::VERSION_ID,
            'version_format' => 'v%%2$s/%%1$s',
        ),
        'ngbm_app_js' => array(
            'base_path' => '/bundles/netgenblockmanagerui/js',
            'version' => BlockManagerUIVersion::VERSION_ID,
            'version_format' => 'v%%2$s/%%1$s',
        ),
        'ngbm_app_vendor' => array(
            'base_path' => '/bundles/netgenblockmanagerui/vendor',
            'version' => null,
            'version_format' => null,
        ),
        'ngcb_css' => array(
            'base_path' => '/bundles/netgencontentbrowserui/css',
            'version' => ContentBrowserUIVersion::VERSION_ID,
            'version_format' => 'v%%2$s/%%1$s',
        ),
        'ngcb_js' => array(
            'base_path' => '/bundles/netgencontentbrowserui/js',
            'version' => ContentBrowserUIVersion::VERSION_ID,
            'version_format' => 'v%%2$s/%%1$s',
        ),
    );

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('assets.packages')) {
            return;
        }

        $packagesService = $container->findDefinition('assets.packages');

        foreach ($this->packages as $name => $package) {
            $container->setDefinition(
                'assets._package_' . $name,
                $this->createPackageDefinition(
                    $package['base_path'],
                    $this->createVersion($container, $name, null, $package['version_format'])
                )
            );

            $packagesService->addMethodCall(
                'addPackage',
                array($name, new Reference('assets._package_' . $name))
            );
        }
    }

    /**
     * Creates a StaticVersionStrategy DI reference.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $name
     * @param string $version
     * @param string $versionFormat
     *
     * @return \Symfony\Component\DependencyInjection\Reference
     */
    protected function createVersion(ContainerBuilder $container, $name, $version, $versionFormat)
    {
        if ($version === null) {
            return new Reference('assets.empty_version_strategy');
        }

        $def = new DefinitionDecorator('assets.static_version_strategy');
        $def
            ->replaceArgument(0, $version)
            ->replaceArgument(1, $versionFormat);

        $container->setDefinition('assets._version_' . $name, $def);

        return new Reference('assets._version_' . $name);
    }

    /**
     * Creates a Package DI definition.
     *
     * @param string $basePath
     * @param \Symfony\Component\DependencyInjection\Reference $version
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    protected function createPackageDefinition($basePath, Reference $version)
    {
        $package = new DefinitionDecorator('assets.path_package');

        return $package
            ->setPublic(false)
            ->replaceArgument(0, $basePath)
            ->replaceArgument(1, $version);
    }
}
