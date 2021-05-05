<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Block;

use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'layoutId' => 84,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'depth' => 2,
                'path' => '/1/22/42/',
                'parentId' => 22,
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'placeholder' => 'top',
                'position' => 4,
                'definitionIdentifier' => 'text',
                'parameters' => [
                    'en' => [
                        'some_param' => 'some_value',
                        'some_other_param' => ['some_other_value'],
                    ],
                ],
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_DRAFT,
            ],
        );

        self::assertSame(42, $block->id);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $block->uuid);
        self::assertSame(84, $block->layoutId);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $block->layoutUuid);
        self::assertSame(2, $block->depth);
        self::assertSame('/1/22/42/', $block->path);
        self::assertSame(22, $block->parentId);
        self::assertSame('cbca9628-3ff1-5440-b1c3-0018331d3544', $block->parentUuid);
        self::assertSame('top', $block->placeholder);
        self::assertSame(4, $block->position);
        self::assertSame('text', $block->definitionIdentifier);
        self::assertSame('default', $block->viewType);
        self::assertSame('standard', $block->itemViewType);
        self::assertSame('My block', $block->name);
        self::assertSame('en', $block->mainLocale);
        self::assertTrue($block->isTranslatable);
        self::assertTrue($block->alwaysAvailable);
        self::assertSame(['en'], $block->availableLocales);
        self::assertSame(Value::STATUS_DRAFT, $block->status);

        self::assertSame(
            [
                'en' => [
                    'some_param' => 'some_value',
                    'some_other_param' => ['some_other_value'],
                ],
            ],
            $block->parameters,
        );
    }
}
