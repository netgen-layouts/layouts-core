<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Block;

use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    public function testSetProperties(): void
    {
        $block = new Block(
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
                    'some_other_param' => 'some_other_value',
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

        $this->assertSame(42, $block->id);
        $this->assertSame(84, $block->layoutId);
        $this->assertSame(2, $block->depth);
        $this->assertSame('/1/22/42/', $block->path);
        $this->assertSame(22, $block->parentId);
        $this->assertSame('top', $block->placeholder);
        $this->assertSame(4, $block->position);
        $this->assertSame('text', $block->definitionIdentifier);
        $this->assertSame('default', $block->viewType);
        $this->assertSame('standard', $block->itemViewType);
        $this->assertSame('My block', $block->name);
        $this->assertSame('en', $block->mainLocale);
        $this->assertTrue($block->alwaysAvailable);
        $this->assertSame(['en'], $block->availableLocales);
        $this->assertSame(Value::STATUS_DRAFT, $block->status);

        $this->assertSame(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $block->parameters
        );
    }
}
