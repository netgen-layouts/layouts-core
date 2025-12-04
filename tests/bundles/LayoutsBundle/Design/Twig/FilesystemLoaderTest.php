<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Design\Twig;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Design\Twig\FilesystemLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Twig\Loader\LoaderInterface;
use Twig\Source;

#[CoversClass(FilesystemLoader::class)]
final class FilesystemLoaderTest extends TestCase
{
    private Stub&LoaderInterface $innerLoaderStub;

    private FilesystemLoader $loader;

    protected function setUp(): void
    {
        $this->innerLoaderStub = self::createStub(LoaderInterface::class);
        $configurationStub = self::createStub(ConfigurationInterface::class);

        $configurationStub
            ->method('getParameter')
            ->with(self::identicalTo('design'))
            ->willReturn('test');

        $this->loader = new FilesystemLoader(
            $this->innerLoaderStub,
            $configurationStub,
        );
    }

    public function testGetSourceContext(): void
    {
        $source = new Source('', '@nglayouts_test/template.html.twig');

        $this->innerLoaderStub
            ->method('getSourceContext')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'))
            ->willReturn($source);

        $sourceContext = $this->loader->getSourceContext('@nglayouts/template.html.twig');

        self::assertSame($source, $sourceContext);
    }

    public function testGetSourceContextWithNonLayoutsTwigFile(): void
    {
        $source = new Source('', '@other/template.html.twig');

        $this->innerLoaderStub
            ->method('getSourceContext')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->willReturn($source);

        $sourceContext = $this->loader->getSourceContext('@other/template.html.twig');

        self::assertSame($source, $sourceContext);
    }

    public function testGetCacheKey(): void
    {
        $this->innerLoaderStub
            ->method('getCacheKey')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'))
            ->willReturn('cache_key');

        $cacheKey = $this->loader->getCacheKey('@nglayouts/template.html.twig');

        self::assertSame('cache_key', $cacheKey);
    }

    public function testGetCacheKeyWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderStub
            ->method('getCacheKey')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->willReturn('cache_key');

        $cacheKey = $this->loader->getCacheKey('@other/template.html.twig');

        self::assertSame('cache_key', $cacheKey);
    }

    public function testIsFresh(): void
    {
        $this->innerLoaderStub
            ->method('isFresh')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'), self::identicalTo(42))
            ->willReturn(true);

        self::assertTrue($this->loader->isFresh('@nglayouts/template.html.twig', 42));
    }

    public function testIsFreshWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderStub
            ->method('isFresh')
            ->with(self::identicalTo('@other/template.html.twig'), self::identicalTo(42))
            ->willReturn(true);

        self::assertTrue($this->loader->isFresh('@other/template.html.twig', 42));
    }

    public function testExists(): void
    {
        $this->innerLoaderStub
            ->method('exists')
            ->with(self::identicalTo('@nglayouts_test/template.html.twig'))
            ->willReturn(true);

        self::assertTrue($this->loader->exists('@nglayouts/template.html.twig'));
    }

    public function testExistsWithNonLayoutsTwigFile(): void
    {
        $this->innerLoaderStub
            ->method('exists')
            ->with(self::identicalTo('@other/template.html.twig'))
            ->willReturn(true);

        self::assertTrue($this->loader->exists('@other/template.html.twig'));
    }
}
