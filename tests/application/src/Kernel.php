<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use function dirname;
use function getenv;
use function is_file;
use function is_string;
use function putenv;
use function str_starts_with;
use function sys_get_temp_dir;
use function trim;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        $databaseUrl = getenv('DATABASE');
        if (!is_string($databaseUrl) || trim($databaseUrl) === '') {
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

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/netgen_layouts/**/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_' . $this->environment . '.yaml');
        } elseif (is_file($path = dirname(__DIR__) . '/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } elseif (is_file($path = dirname(__DIR__) . '/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }

    protected function getContainerBaseClass(): string
    {
        return '\\' . MockerContainer::class;
    }
}
