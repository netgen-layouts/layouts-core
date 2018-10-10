<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Kernel;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

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

    public function registerBundles()
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function getContainerBaseClass()
    {
        return '\\' . MockerContainer::class;
    }

    protected function configureDatabase(ContainerBuilder $container)
    {
        $databaseUrl = (string) getenv('DATABASE');
        $databaseUrl = $databaseUrl ?: 'sqlite:///' . $this->getCacheDir() . '/ngbm.db';
        putenv('DATABASE=' . $databaseUrl);

        $databaseCharset = mb_stripos($databaseUrl, 'mysql://') === 0 ? 'utf8mb4' : 'utf8';
        $container->setParameter('database_charset', $databaseCharset);

        if (self::VERSION_ID < 30200) {
            /*
             * @deprecated Symfony 2.8 does not have kernel.project_dir parameter,
             * so we need to set the parameter to the container manually
             */
            $container->setParameter('kernel.project_dir', $this->getProjectDir());

            /*
             * @deprecated Symfony 2.8 does not support runtime environment variables,
             * so we need to set the database parameter to the container manually
             */
            $container->setParameter('env(resolve:DATABASE)', $databaseUrl);
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');

        $this->configureDatabase($container);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }
}
