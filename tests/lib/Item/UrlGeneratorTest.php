<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\Item\UrlGenerator;
use Netgen\BlockManager\Tests\Item\Stubs\ValueUrlGenerator;
use PHPUnit\Framework\TestCase;

final class UrlGeneratorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    public function setUp(): void
    {
        $this->urlGenerator = new UrlGenerator(
            ['value' => new ValueUrlGenerator()]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::__construct
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     */
    public function testGenerate(): void
    {
        $this->assertSame(
            '/item-url',
            $this->urlGenerator->generate(
                new CmsItem(['valueType' => 'value'])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::__construct
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     */
    public function testGenerateWithNullCmsItem(): void
    {
        $this->assertNull(
            $this->urlGenerator->generate(
                new NullCmsItem('value')
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value type "unknown" does not exist.
     */
    public function testGenerateWithNoUrlGenerator(): void
    {
        $this->urlGenerator->generate(
            new CmsItem(['valueType' => 'unknown'])
        );
    }
}
