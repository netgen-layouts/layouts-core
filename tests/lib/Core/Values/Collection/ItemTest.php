<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Item\Item as CmsItem;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Parameters\Parameter;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Item());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::__construct
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCmsItem
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::getValue
     */
    public function testSetProperties(): void
    {
        $cmsItem = new CmsItem();
        $definition = new ItemDefinition();

        $item = new Item(
            [
                'id' => 42,
                'collectionId' => 30,
                'definition' => $definition,
                'position' => 3,
                'type' => Item::TYPE_OVERRIDE,
                'value' => 32,
                'cmsItem' => function () use ($cmsItem): ItemInterface {
                    return $cmsItem;
                },
            ]
        );

        $this->assertSame(42, $item->getId());
        $this->assertSame(30, $item->getCollectionId());
        $this->assertSame($definition, $item->getDefinition());
        $this->assertSame(3, $item->getPosition());
        $this->assertSame(Item::TYPE_OVERRIDE, $item->getType());
        $this->assertSame(32, $item->getValue());
        $this->assertSame($cmsItem, $item->getCmsItem());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isVisible
     * @dataProvider visibilityProvider
     */
    public function testVisibility(
        string $visibilityStatus,
        DateTimeInterface $visibleFrom = null,
        DateTimeInterface $visibleTo = null,
        bool $itemVisible = true
    ): void {
        $item = new Item(
            [
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => $visibilityStatus,
                                    ]
                                ),
                                'visible_from' => new Parameter(
                                    [
                                        'value' => $visibleFrom,
                                    ]
                                ),
                                'visible_to' => new Parameter(
                                    [
                                        'value' => $visibleTo,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->assertSame($itemVisible, $item->isVisible(new DateTimeImmutable('@15000')));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isScheduled
     * @dataProvider scheduledProvider
     */
    public function testIsScheduled(
        string $visibilityStatus,
        bool $itemScheduled = true
    ): void {
        $item = new Item(
            [
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => $visibilityStatus,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->assertSame($itemScheduled, $item->isScheduled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isScheduled
     */
    public function testIsScheduledWithoutConfig(): void
    {
        $item = new Item();

        $this->assertFalse($item->isScheduled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isVisible
     */
    public function testIsVisibleWithoutConfig(): void
    {
        $item = new Item();

        $this->assertTrue($item->isVisible());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isValid
     * @dataProvider isValidProvider
     */
    public function testIsValid(bool $itemVisible, bool $cmsItemVisible, bool $isValid): void
    {
        $item = new Item(
            [
                'definition' => new ItemDefinition(),
                'cmsItem' => new CmsItem(['isVisible' => $cmsItemVisible]),
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => $itemVisible ? Item::VISIBILITY_VISIBLE : Item::VISIBILITY_HIDDEN,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->assertSame($isValid, $item->isValid());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Item::isValid
     * @dataProvider isValidProvider
     */
    public function testIsValidWithNullItem(bool $itemVisible): void
    {
        $item = new Item(
            [
                'cmsItem' => new NullItem('value'),
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => $itemVisible ? Item::VISIBILITY_VISIBLE : Item::VISIBILITY_HIDDEN,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->assertFalse($item->isValid());
    }

    public function isValidProvider(): array
    {
        return [
            [true, true, true],
            [false, true, false],
            [true, false, false],
            [false, false, false],
        ];
    }

    public function visibilityProvider(): array
    {
        return [
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@10000'), new DateTimeImmutable('@20000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@17000'), new DateTimeImmutable('@20000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@10000'), new DateTimeImmutable('@13000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@10000'), new DateTimeImmutable('@15000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@15000'), new DateTimeImmutable('@20000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@20000'), new DateTimeImmutable('@10000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@20000'), new DateTimeImmutable('@17000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@13000'), new DateTimeImmutable('@10000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@15000'), new DateTimeImmutable('@10000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@20000'), new DateTimeImmutable('@15000'), true],
            [Item::VISIBILITY_VISIBLE, null, new DateTimeImmutable('@20000'), true],
            [Item::VISIBILITY_VISIBLE, null, new DateTimeImmutable('@15000'), true],
            [Item::VISIBILITY_VISIBLE, null, new DateTimeImmutable('@10000'), true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@10000'), null, true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@15000'), null, true],
            [Item::VISIBILITY_VISIBLE, new DateTimeImmutable('@20000'), null, true],
            [Item::VISIBILITY_VISIBLE, null, null, true],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@10000'), new DateTimeImmutable('@20000'), true],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@17000'), new DateTimeImmutable('@20000'), false],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@10000'), new DateTimeImmutable('@13000'), false],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@10000'), new DateTimeImmutable('@15000'), true],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@15000'), new DateTimeImmutable('@20000'), true],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@20000'), new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@20000'), new DateTimeImmutable('@17000'), false],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@13000'), new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@15000'), new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@20000'), new DateTimeImmutable('@15000'), false],
            [Item::VISIBILITY_SCHEDULED, null, new DateTimeImmutable('@20000'), true],
            [Item::VISIBILITY_SCHEDULED, null, new DateTimeImmutable('@15000'), true],
            [Item::VISIBILITY_SCHEDULED, null, new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@10000'), null, true],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@15000'), null, true],
            [Item::VISIBILITY_SCHEDULED, new DateTimeImmutable('@20000'), null, false],
            [Item::VISIBILITY_SCHEDULED, null, null, true],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@10000'), new DateTimeImmutable('@20000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@17000'), new DateTimeImmutable('@20000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@10000'), new DateTimeImmutable('@13000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@10000'), new DateTimeImmutable('@15000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@15000'), new DateTimeImmutable('@20000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@20000'), new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@20000'), new DateTimeImmutable('@17000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@13000'), new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@15000'), new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@20000'), new DateTimeImmutable('@15000'), false],
            [Item::VISIBILITY_HIDDEN, null, new DateTimeImmutable('@20000'), false],
            [Item::VISIBILITY_HIDDEN, null, new DateTimeImmutable('@15000'), false],
            [Item::VISIBILITY_HIDDEN, null, new DateTimeImmutable('@10000'), false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@10000'), null, false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@15000'), null, false],
            [Item::VISIBILITY_HIDDEN, new DateTimeImmutable('@20000'), null, false],
            [Item::VISIBILITY_HIDDEN, null, null, false],
        ];
    }

    public function scheduledProvider(): array
    {
        return [
            [Item::VISIBILITY_VISIBLE, false],
            [Item::VISIBILITY_HIDDEN, false],
            [Item::VISIBILITY_SCHEDULED, true],
        ];
    }
}
