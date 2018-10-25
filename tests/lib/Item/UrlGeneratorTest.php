<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\Item\UrlGenerator;
use Netgen\BlockManager\Tests\Item\Stubs\ValueUrlGenerator;
use PHPUnit\Framework\TestCase;
use stdClass;

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
        self::assertSame(
            '/item-url',
            $this->urlGenerator->generate(
                CmsItem::fromArray(['valueType' => 'value', 'object' => new stdClass()])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::__construct
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     */
    public function testGenerateWithNullCmsItem(): void
    {
        self::assertSame('', $this->urlGenerator->generate(new NullCmsItem('value')));
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::__construct
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     */
    public function testGenerateWithNullObject(): void
    {
        self::assertSame('', $this->urlGenerator->generate(CmsItem::fromArray(['object' => null])));
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     */
    public function testGenerateWithNoUrlGenerator(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value type "unknown" does not exist.');

        $this->urlGenerator->generate(
            CmsItem::fromArray(
                ['valueType' => 'unknown', 'object' => new stdClass()]
            )
        );
    }
}
