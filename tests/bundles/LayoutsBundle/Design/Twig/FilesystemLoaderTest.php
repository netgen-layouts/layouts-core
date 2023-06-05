<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Design\Twig;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Loader\LoaderInterface;
use Twig\Source;

final class FilesystemLoaderTest extends TestCase
{
    private MockObject $innerLoaderMock;

    private MockObject $configurationMock;

    private FilesystemLoader $loader;

    protected function setUp(): void
    {
        $this->innerLoaderMock = $this->createMock(LoaderInterface::class);
        $this->configurationMock = $this->createMock(ConfigurationInterface::class);

        $this->configurationMock
            ->method('getParameter')
            ->with(self::identicalTo('design'))
            ->willReturn('test');

        $this->loader = new FilesystemLoader(
            $this->innerLoaderMock,
            $this->configurationMock,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getSourceContext
     */
    public function testGetSourceContext(): void
    {
        $source = new Source('', '@nglayouts_test/template.html.twig');

        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getSourceContext')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'))
            ->willReturn($source);

        $sourceContext = $this->loader->getSourceContext('@nglayouts/template.html.twig');

        self::assertSame($source, $sourceContext);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getSourceContext
     */
    public function testGetSourceContextWithNonLayoutsTwigFile(): void
    {
        $source = new Source('', '@other/template.html.twig');

        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getSourceContext')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->willReturn($source);

        $sourceContext = $this->loader->getSourceContext('@other/template.html.twig');

        self::assertSame($source, $sourceContext);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getCacheKey
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testGetCacheKey(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getCacheKey')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'))
            ->willReturn('cache_key');

        $cacheKey = $this->loader->getCacheKey('@nglayouts/template.html.twig');

        self::assertSame('cache_key', $cacheKey);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getCacheKey
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testGetCacheKeyWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('getCacheKey')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->willReturn('cache_key');

        $cacheKey = $this->loader->getCacheKey('@other/template.html.twig');

        self::assertSame('cache_key', $cacheKey);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::isFresh
     */
    public function testIsFresh(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('isFresh')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'), self::identicalTo(42))
            ->willReturn(true);

        self::assertTrue($this->loader->isFresh('@nglayouts/template.html.twig', 42));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::isFresh
     */
    public function testIsFreshWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('isFresh')
            ->with(self::identicalTo('@other/template.html.twig'), self::identicalTo(42))
            ->willReturn(true);

        self::assertTrue($this->loader->isFresh('@other/template.html.twig', 42));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::exists
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testExists(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('exists')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'))
            ->willReturn(true);

        self::assertTrue($this->loader->exists('@nglayouts/template.html.twig'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::exists
     * @covers \Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader::getRealName
     */
    public function testExistsWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderMock
            ->expects(self::once())
            ->method('exists')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->willReturn(true);

        self::assertTrue($this->loader->exists('@other/template.html.twig'));
    }
}
