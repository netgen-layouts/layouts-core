<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Design\Twig;

use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader as BaseFilesystemLoader;
use Twig\Source;

class FilesystemLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $innerLoaderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configurationMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader
     */
    private $loader;

    public function setUp()
    {
        // We mock \Twig\Loader\FilesystemLoader instead of the loader interface
        // because it has all needed methods in both versions of Twig (1.x and 2.x)
        $this->innerLoaderMock = $this->createMock(BaseFilesystemLoader::class);
        $this->configurationMock = $this->createMock(ConfigurationInterface::class);

        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('design'))
            ->will($this->returnValue('test'));

        $this->loader = new FilesystemLoader(
            $this->innerLoaderMock,
            $this->configurationMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getSourceContext
     */
    public function testGetSourceContext()
    {
        $this->innerLoaderMock
            ->expects($this->once())
            ->method('getSourceContext')
            ->with($this->equalTo('@ngbm_test/template.html.twig'))
            ->will($this->returnValue(new Source('', '@ngbm_test/template.html.twig')));

        $sourceContext = $this->loader->getSourceContext('@ngbm/template.html.twig');

        $this->assertEquals(new Source('', '@ngbm_test/template.html.twig'), $sourceContext);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::getCacheKey
     */
    public function testGetCacheKey()
    {
        $this->innerLoaderMock
            ->expects($this->once())
            ->method('getCacheKey')
            ->with($this->equalTo('@ngbm_test/template.html.twig'))
            ->will($this->returnValue('cache_key'));

        $cacheKey = $this->loader->getCacheKey('@ngbm/template.html.twig');

        $this->assertEquals('cache_key', $cacheKey);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::isFresh
     */
    public function testIsFresh()
    {
        $this->innerLoaderMock
            ->expects($this->once())
            ->method('isFresh')
            ->with($this->equalTo('@ngbm_test/template.html.twig'), $this->equalTo(42))
            ->will($this->returnValue(true));

        $this->assertTrue($this->loader->isFresh('@ngbm/template.html.twig', 42));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Design\Twig\FilesystemLoader::exists
     */
    public function testExists()
    {
        $this->innerLoaderMock
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('@ngbm_test/template.html.twig'))
            ->will($this->returnValue(true));

        $this->assertTrue($this->loader->exists('@ngbm/template.html.twig'));
    }
}