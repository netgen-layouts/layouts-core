<?php

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Core\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;

final class CollectionItemNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->normalizer = new CollectionItemNormalizer(
            $this->urlGeneratorMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::normalize
     */
    public function testNormalize()
    {
        $item = new CollectionItem(
            [
                'id' => 42,
                'collectionId' => 24,
                'position' => 3,
                'type' => CollectionItem::TYPE_OVERRIDE,
                'value' => 12,
                'definition' => new ItemDefinition(['valueType' => 'ezcontent']),
                'cmsItem' => new Item(
                    [
                        'name' => 'Value name',
                        'isVisible' => true,
                        'value' => 12,
                        'valueType' => 'ezcontent',
                    ]
                ),
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->urlGeneratorMock
            ->expects($this->any())
            ->method('generate')
            ->with($this->equalTo($item->getCmsItem()))
            ->will($this->returnValue('/some/url'));

        $this->assertEquals(
            [
                'id' => $item->getId(),
                'collection_id' => $item->getCollectionId(),
                'position' => $item->getPosition(),
                'type' => $item->getType(),
                'value' => $item->getValue(),
                'value_type' => $item->getDefinition()->getValueType(),
                'visible' => $item->isVisible(),
                'scheduled' => $item->isScheduled(),
                'name' => 'Value name',
                'cms_url' => '/some/url',
                'cms_visible' => true,
            ],
            $this->normalizer->normalize(new VersionedValue($item, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::normalize
     */
    public function testNormalizeWithNoValue()
    {
        $item = new CollectionItem(
            [
                'id' => 42,
                'collectionId' => 24,
                'position' => 3,
                'type' => CollectionItem::TYPE_OVERRIDE,
                'value' => 12,
                'definition' => new ItemDefinition(['valueType' => 'ezcontent']),
                'cmsItem' => new NullItem(12),
                'configs' => [
                    'visibility' => new Config(
                        [
                            'parameters' => [
                                'visibility_status' => new Parameter(
                                    [
                                        'value' => CollectionItem::VISIBILITY_VISIBLE,
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->urlGeneratorMock
            ->expects($this->never())
            ->method('generate');

        $this->assertEquals(
            [
                'id' => $item->getId(),
                'collection_id' => $item->getCollectionId(),
                'position' => $item->getPosition(),
                'type' => $item->getType(),
                'value' => $item->getValue(),
                'value_type' => 'null',
                'visible' => $item->isVisible(),
                'scheduled' => $item->isScheduled(),
                'name' => '(INVALID ITEM)',
                'cms_url' => null,
                'cms_visible' => true,
            ],
            $this->normalizer->normalize(new VersionedValue($item, 1))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\CollectionItemNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, $expected)
    {
        $this->assertEquals($expected, $this->normalizer->supportsNormalization($data));
    }

    /**
     * Provider for {@link self::testSupportsNormalization}.
     *
     * @return array
     */
    public function supportsNormalizationProvider()
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new CollectionItem(), false],
            [new VersionedValue(new APIValue(), 1), false],
            [new VersionedValue(new CollectionItem(), 2), false],
            [new VersionedValue(new CollectionItem(), 1), true],
        ];
    }
}
