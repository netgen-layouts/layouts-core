<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime\Loader;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader\ContainerRuntimeLoader;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\Container;

final class ContainerRuntimeLoaderTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader\ContainerRuntimeLoader
     */
    private $runtimeLoader;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function setUp(): void
    {
        $this->container = new Container();

        $this->runtimeLoader = new ContainerRuntimeLoader($this->container);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader\ContainerRuntimeLoader::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader\ContainerRuntimeLoader::addRuntime
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader\ContainerRuntimeLoader::load
     */
    public function testLoad(): void
    {
        $service = new stdClass();
        $this->container->set('test', $service);

        $this->runtimeLoader->addRuntime(stdClass::class, 'test');

        $this->assertSame($service, $this->runtimeLoader->load(stdClass::class));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader\ContainerRuntimeLoader::load
     */
    public function testLoadWithNoMapping(): void
    {
        $this->assertNull($this->runtimeLoader->load(stdClass::class));
    }
}
