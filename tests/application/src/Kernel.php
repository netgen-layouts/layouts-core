<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App;

use Behat\Config\Config;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function class_exists;
use function dirname;
use function getenv;
use function is_string;
use function mb_trim;
use function putenv;
use function str_starts_with;
use function sys_get_temp_dir;

final class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait {
        configureContainer as private configureKernelContainer;
    }

    public function boot(): void
    {
        parent::boot();

        $databaseUrl = getenv('DATABASE');
        if (!is_string($databaseUrl) || mb_trim($databaseUrl) === '') {
            $databaseUrl = 'sqlite:///' . $this->getCacheDir() . '/nglayouts.db';
        }

        putenv('DATABASE=' . $databaseUrl);

        $databaseCharset = str_starts_with($databaseUrl, 'mysql://') ? 'utf8mb4' : 'utf8';
        putenv('DATABASE_CHARSET=' . $databaseCharset);
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/nglayouts/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/nglayouts/logs';
    }

    public function process(ContainerBuilder $container): void
    {
        $container->removeDefinition('netgen_layouts.event_listener.app_csrf_validation_listener');
    }

    public function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $this->configureKernelContainer($container, $loader, $builder);

        $configDir = $this->getConfigDir();

        $container->import($configDir . '/{services}/netgen_layouts.yaml');

        if (class_exists(Config::class)) {
            $container->import($configDir . '/{services}/behat/**/*.yaml');
        }
    }
}
