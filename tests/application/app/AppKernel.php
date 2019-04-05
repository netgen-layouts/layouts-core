<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            // Symfony

            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\WebServerBundle\WebServerBundle(),

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
            new \FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle(),
        ];
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

        if ($this->getEnvironment() === 'test') {
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
    }
}
