<?php

namespace Netgen\BlockManager\Tests\Kernel;

use Symfony\Bundle\WebServerBundle\WebServerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // Symfony

            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),

            // Other dependencies

            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Netgen Layouts

            new \Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle(),
            new \Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle(),
            new \Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle(),
            new \Netgen\Bundle\BlockManagerUIBundle\NetgenBlockManagerUIBundle(),
            new \Netgen\Bundle\BlockManagerAdminBundle\NetgenBlockManagerAdminBundle(),
            new \Netgen\Bundle\BlockManagerFixturesBundle\NetgenBlockManagerFixturesBundle(),
            new \Netgen\Bundle\BlockManagerStandardBundle\NetgenBlockManagerStandardBundle(),
        ];

        // @deprecated Remove class_exists check when support for Symfony 2.8 ends
        if (class_exists(WebServerBundle::class)) {
            $bundles[] = new WebServerBundle();
        }

        return $bundles;
    }

    public function boot()
    {
        parent::boot();

        $databaseUrl = getenv('DATABASE');
        $databaseUrl = $databaseUrl ?: 'sqlite:///' . $this->getCacheDir() . '/ngbm.db';
        putenv('DATABASE=' . $databaseUrl);
    }

    public function getProjectDir()
    {
        return dirname(__DIR__);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/ngbm/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/ngbm/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    protected function getContainerBaseClass()
    {
        return '\\' . MockerContainer::class;
    }

    protected function prepareContainer(ContainerBuilder $container)
    {
        parent::prepareContainer($container);

        if (Kernel::VERSION_ID < 30200) {
            /*
             * @deprecated Symfony 2.8 does not have kernel.project_dir parameter,
             * so we need to set the parameter to the container manually
             */
            $container->setParameter('kernel.project_dir', $this->getProjectDir());

            /*
             * @deprecated Symfony 2.8 does not support runtime environment variables,
             * so we need to set the database parameter to the container manually
             */
            $container->setParameter('env(DATABASE)', getenv('DATABASE'));
        }
    }
}
