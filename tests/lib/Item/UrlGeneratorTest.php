<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Item\UrlGenerator;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Tests\Item\Stubs\ValueUrlGenerator;
use Netgen\Layouts\Tests\Stubs\Container;
use PHPUnit\Framework\TestCase;
use stdClass;

final class UrlGeneratorTest extends TestCase
{
    private UrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGenerator = new UrlGenerator(
            new Container(['value' => new ValueUrlGenerator()]),
        );
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
    public function testGenerateDefaultUrl(): void
    {
        $url = $this->urlGenerator->generate(
            CmsItem::fromArray(['valueType' => 'value', 'object' => new stdClass()]),
        );

        self::assertSame('/item-url', $url);
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
    public function testGenerateAdminUrl(): void
    {
        $url = $this->urlGenerator->generate(
            CmsItem::fromArray(['valueType' => 'value', 'object' => new stdClass()]),
            UrlGeneratorInterface::TYPE_ADMIN,
        );

        self::assertSame('/admin/item-url', $url);
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
    public function testGenerateWithNullCmsItem(): void
    {
        $url = $this->urlGenerator->generate(
            new NullCmsItem('value'),
            UrlGeneratorInterface::TYPE_ADMIN,
        );

        self::assertSame('', $url);
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
    public function testGenerateWithNullObject(): void
    {
        $url = $this->urlGenerator->generate(
            CmsItem::fromArray(['object' => null]),
            UrlGeneratorInterface::TYPE_ADMIN,
        );

        self::assertSame('', $url);
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
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

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
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

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
    public function testGenerateThrowsItemExceptionWithInvalidUrlType(): void
    {
        $this->expectException(ItemException::class);
        $this->expectExceptionMessage('"unknown" URL type is invalid for "value" value type.');

        $this->urlGenerator = new UrlGenerator(
            new Container(['value' => new ValueUrlGenerator()]),
        );

        $this->urlGenerator->generate(
            CmsItem::fromArray(
                ['valueType' => 'value', 'object' => new stdClass()],
            ),
            'unknown',
        );
    }
}
