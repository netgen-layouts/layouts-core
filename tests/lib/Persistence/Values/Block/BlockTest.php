<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Block;

use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    public function testSetProperties()
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

        $this->assertEquals(42, $block->id);
        $this->assertEquals(84, $block->layoutId);
        $this->assertEquals(2, $block->depth);
        $this->assertEquals('/1/22/42/', $block->path);
        $this->assertEquals(22, $block->parentId);
        $this->assertEquals('top', $block->placeholder);
        $this->assertEquals(4, $block->position);
        $this->assertEquals('text', $block->definitionIdentifier);
        $this->assertEquals('default', $block->viewType);
        $this->assertEquals('standard', $block->itemViewType);
        $this->assertEquals('My block', $block->name);
        $this->assertEquals('en', $block->mainLocale);
        $this->assertTrue($block->alwaysAvailable);
        $this->assertEquals(['en'], $block->availableLocales);
        $this->assertEquals(Value::STATUS_DRAFT, $block->status);

        $this->assertEquals(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $block->parameters
        );
    }
}
