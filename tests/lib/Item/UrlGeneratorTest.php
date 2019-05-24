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
    /**
     * @var \Netgen\Layouts\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGenerator = new UrlGenerator(
            new Container(['value' => new ValueUrlGenerator()])
        );
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
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
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     */
    public function testGenerateWithNullCmsItem(): void
    {
        self::assertSame('', $this->urlGenerator->generate(new NullCmsItem('value')));
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::__construct
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
     */
    public function testGenerateWithNullObject(): void
    {
        self::assertSame('', $this->urlGenerator->generate(CmsItem::fromArray(['object' => null])));
    }

    /**
     * @covers \Netgen\Layouts\Item\UrlGenerator::generate
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
