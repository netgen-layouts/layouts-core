<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Kernel;

use FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle;
use Symfony\Bundle\WebServerBundle\WebServerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function registerBundles(): iterable
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
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Netgen Layouts

            new \Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle(),
            new \Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle(),
            new \Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle(),
            new \Netgen\Bundle\BlockManagerUIBundle\NetgenBlockManagerUIBundle(),
            new \Netgen\Bundle\BlockManagerAdminBundle\NetgenBlockManagerAdminBundle(),
            new \Netgen\Bundle\BlockManagerStandardBundle\NetgenBlockManagerStandardBundle(),

            // Test bundles

            new \Netgen\BlockManager\Tests\Bundle\FixturesBundle\FixturesBundle(),
        ];

        // @deprecated Remove class_exists checks when support for Symfony 2.8 ends

        if (class_exists(FriendsOfBehatSymfonyExtensionBundle::class)) {
            $bundles[] = new FriendsOfBehatSymfonyExtensionBundle();
        }

        if (class_exists(WebServerBundle::class)) {
            $bundles[] = new WebServerBundle();
        }

        return $bundles;
    }

    public function boot(): void
    {
        parent::boot();

        $databaseUrl = getenv('DATABASE');
        $databaseUrl = $databaseUrl ?: 'sqlite:///' . $this->getCacheDir() . '/ngbm.db';
        putenv('DATABASE=' . $databaseUrl);
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/ngbm/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/ngbm/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');

        // @deprecated Symfony 2.8 is not compatible with Behat config, remove
        // the check for kernel version when support for Symfony 2.8 ends
        if ($this->getEnvironment() === 'test' && Kernel::VERSION_ID >= 30400) {
            $loader->load(__DIR__ . '/config/test/services.yml');
        }
    }

    protected function getContainerBaseClass(): string
    {
        return '\\' . MockerContainer::class;
    }

    protected function prepareContainer(ContainerBuilder $container): void
    {
        parent::prepareContainer($container);

        $database = (string) getenv('DATABASE');

        $databaseCharset = mb_stripos($database, 'mysql://') === 0 ? 'utf8mb4' : 'utf8';
        $container->setParameter('database_charset', $databaseCharset);

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
            $container->setParameter('env(DATABASE)', $database);
        }
    }
}
