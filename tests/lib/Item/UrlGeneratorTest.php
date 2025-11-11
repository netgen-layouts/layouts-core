<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Item\UrlGenerator;
use Netgen\Layouts\Item\UrlType;
use Netgen\Layouts\Tests\Item\Stubs\ValueUrlGenerator;
use Netgen\Layouts\Tests\Stubs\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(UrlGenerator::class)]
final class UrlGeneratorTest extends TestCase
{
    private UrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGenerator = new UrlGenerator(
            new Container(['value' => new ValueUrlGenerator()]),
        );
    }

    public function testGenerateDefaultUrl(): void
    {
        $url = $this->urlGenerator->generate(
            CmsItem::fromArray(['valueType' => 'value', 'object' => new stdClass()]),
        );

        self::assertSame('/item-url', $url);
    }

    public function testGenerateAdminUrl(): void
    {
        $url = $this->urlGenerator->generate(
            CmsItem::fromArray(['valueType' => 'value', 'object' => new stdClass()]),
            UrlType::Admin,
        );

        self::assertSame('/admin/item-url', $url);
    }

    public function testGenerateWithNullCmsItem(): void
    {
        $url = $this->urlGenerator->generate(
            new NullCmsItem('value'),
            UrlType::Admin,
        );

        self::assertSame('', $url);
    }

    public function testGenerateWithNullObject(): void
    {
        $url = $this->urlGenerator->generate(
            CmsItem::fromArray(['object' => null]),
            UrlType::Admin,
        );

        self::assertSame('', $url);
    }

    public function testGenerateThrowsItemExceptionWithNoUrlGenerator(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value URL generator for "unknown" value type does not exist.');

        $this->urlGenerator->generate(
            CmsItem::fromArray(
                ['valueType' => 'unknown', 'object' => new stdClass()],
            ),
        );
    }

    public function testGenerateThrowsItemExceptionWithInvalidUrlGenerator(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('Value URL generator for "value" value type does not exist.');

        $this->urlGenerator = new UrlGenerator(
            new Container(['value' => new stdClass()]),
        );

        $this->urlGenerator->generate(
            CmsItem::fromArray(
                ['valueType' => 'value', 'object' => new stdClass()],
            ),
        );
    }
}
