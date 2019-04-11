<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Block;

use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    public function testSetProperties(): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'layoutId' => 84,
                'depth' => 2,
                'path' => '/1/22/42/',
                'parentId' => 22,
                'placeholder' => 'top',
                'position' => 4,
                'definitionIdentifier' => 'text',
                'parameters' => [
                    'some_param' => 'some_value',
                    'some_other_param' => ['some_other_value'],
                ],
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_DRAFT,
            ]
        );

        self::assertSame(42, $block->id);
        self::assertSame(84, $block->layoutId);
        self::assertSame(2, $block->depth);
        self::assertSame('/1/22/42/', $block->path);
        self::assertSame(22, $block->parentId);
        self::assertSame('top', $block->placeholder);
        self::assertSame(4, $block->position);
        self::assertSame('text', $block->definitionIdentifier);
        self::assertSame('default', $block->viewType);
        self::assertSame('standard', $block->itemViewType);
        self::assertSame('My block', $block->name);
        self::assertSame('en', $block->mainLocale);
        self::assertTrue($block->alwaysAvailable);
        self::assertSame(['en'], $block->availableLocales);
        self::assertSame(Value::STATUS_DRAFT, $block->status);

        self::assertSame(
            [
                'some_param' => 'some_value',
                'some_other_param' => ['some_other_value'],
            ],
            $block->parameters
        );
    }
}
