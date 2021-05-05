<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Item\UrlGenerator;
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
    public function testGenerate(): void
    {
        self::assertSame(
            '/item-url',
            $this->urlGenerator->generate(
                CmsItem::fromArray(['valueType' => 'value', 'object' => new stdClass()]),
            ),
        );
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
    public function testGenerateWithNullCmsItem(): void
    {
        self::assertSame('', $this->urlGenerator->generate(new NullCmsItem('value')));
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     * @covers \Netgen\Layouts\Item\UrlGenerator::getValueUrlGenerator
     */
    public function testGenerateWithNullObject(): void
    {
        self::assertSame('', $this->urlGenerator->generate(CmsItem::fromArray(['object' => null])));
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
}
