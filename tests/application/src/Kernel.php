<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function dirname;
use function getenv;
use function is_string;
use function putenv;
use function str_starts_with;
use function sys_get_temp_dir;
use function trim;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait {
        configureContainer as protected configureBaseContainer;
    }

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

    protected function getContainerBaseClass(): string
    {
        return '\\' . MockerContainer::class;
    }

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $this->configureBaseContainer($container, $loader, $builder);
        $container->import($this->getConfigDir() . '/{packages}/netgen_layouts/**/*.{php,yaml}');
    }
}
