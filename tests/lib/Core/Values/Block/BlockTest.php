<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Block());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollections
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     */
    public function testDefaultProperties(): void
    {
        $block = new Block();

        $this->assertSame([], $block->getPlaceholders());
        $this->assertSame([], $block->getCollections());
        $this->assertSame([], $block->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollections
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isTranslatable
     */
    public function testSetProperties(): void
    {
        $definition = new BlockDefinition();

        $placeholder = new Placeholder(['identifier' => 'main']);
        $collection = new Collection(['id' => 42]);

        $block = new Block(
            [
                'id' => 42,
                'layoutId' => 24,
                'definition' => $definition,
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parentPosition' => 3,
                'placeholders' => [
                    'main' => $placeholder,
                ],
                'collections' => new ArrayCollection(
                    ['default' => $collection]
                ),
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [],
            ]
        );

        $this->assertSame(42, $block->getId());
        $this->assertSame(24, $block->getLayoutId());
        $this->assertSame($definition, $block->getDefinition());
        $this->assertSame($placeholder, $block->getPlaceholder('main'));
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertTrue($block->hasPlaceholder('main'));
        $this->assertSame($collection, $block->getCollection('default'));
        $this->assertFalse($block->hasCollection('test'));
        $this->assertTrue($block->hasCollection('default'));
        $this->assertSame('default', $block->getViewType());
        $this->assertSame('standard', $block->getItemViewType());
        $this->assertSame('My block', $block->getName());
        $this->assertSame(3, $block->getParentPosition());
        $this->assertTrue($block->isTranslatable());
        $this->assertSame('en', $block->getMainLocale());
        $this->assertTrue($block->isAlwaysAvailable());
        $this->assertSame(['en'], $block->getAvailableLocales());
        $this->assertSame('en', $block->getLocale());

        $this->assertSame(
            [
                'main' => $placeholder,
            ],
            $block->getPlaceholders()
        );

        try {
            $block->getPlaceholder('test');
        } catch (BlockException $e) {
            // Do nothing
        }

        $this->assertSame(
            [
                'default' => $collection,
            ],
            $block->getCollections()
        );

        try {
            $block->getCollection('test');
        } catch (BlockException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::buildDynamicParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasDynamicParameter
     */
    public function testGetDynamicParameter(): void
    {
        $block = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ]
                ),
            ]
        );

        $this->assertTrue($block->hasDynamicParameter('definition_param'));
        $this->assertSame('definition_value', $block->getDynamicParameter('definition_param'));

        $this->assertTrue($block->hasDynamicParameter('closure_param'));
        $this->assertSame('closure_value', $block->getDynamicParameter('closure_param'));

        $this->assertNull($block->getDynamicParameter('unknown_param'));
        $this->assertFalse($block->hasDynamicParameter('unknown_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isContextual
     */
    public function testIsContextual(): void
    {
        $query = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ]
                ),
            ]
        );

        $this->assertFalse($query->isContextual());
    }
}
