<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Normalizer\V1;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use Netgen\BlockManager\Tests\Serializer\Stubs\NormalizerStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

final class BlockNormalizerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $normalizerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer
     */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->normalizer = new BlockNormalizer($this->blockServiceMock);
        $this->normalizer->setSerializer(new Serializer([new NormalizerStub()]));
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer::setSerializer
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::__construct
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::getBlockCollections
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => 24,
                'status' => Value::STATUS_PUBLISHED,
                'offset' => 10,
                'limit' => 5,
            ]
        );

        $placeholder = Placeholder::fromArray(['identifier' => 'main']);

        $block = Block::fromArray(
            [
                'id' => 42,
                'layoutId' => 24,
                'definition' => BlockDefinition::fromArray(['identifier' => 'definition']),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'position' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'placeholders' => [
                    'main' => $placeholder,
                ],
                'collections' => new ArrayCollection(
                    ['default' => $collection]
                ),
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => [
                    'param' => new Parameter(),
                ],
                'configs' => [
                    'config_key' => Config::fromArray(
                        [
                            'parameters' => [
                                'param' => new Parameter(),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->blockServiceMock
            ->expects(self::once())
            ->method('hasPublishedState')
            ->with(self::identicalTo($block))
            ->will(self::returnValue(true));

        self::assertSame(
            [
                'id' => $block->getId(),
                'layout_id' => $block->getLayoutId(),
                'definition_identifier' => $block->getDefinition()->getIdentifier(),
                'name' => $block->getName(),
                'parent_position' => $block->getPosition(),
                'parameters' => ['param' => 'data'],
                'view_type' => $block->getViewType(),
                'item_view_type' => $block->getItemViewType(),
                'published' => true,
                'has_published_state' => true,
                'locale' => $block->getLocale(),
                'is_translatable' => $block->isTranslatable(),
                'always_available' => $block->isAlwaysAvailable(),
                'is_container' => false,
                'placeholders' => ['data'],
                'collections' => [
                    [
                        'identifier' => 'default',
                        'collection_id' => $collection->getId(),
                        'collection_type' => Collection::TYPE_MANUAL,
                        'offset' => $collection->getOffset(),
                        'limit' => $collection->getLimit(),
                    ],
                ],
                'config' => ['config_key' => ['param' => 'data']],
            ],
            $this->normalizer->normalize(new VersionedValue($block, 1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::normalize
     */
    public function testNormalizeWithContainerBlock(): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'layoutId' => 24,
                'definition' => ContainerDefinition::fromArray(
                    [
                        'identifier' => 'definition',
                        'handler' => new ContainerDefinitionHandler(),
                    ]
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'position' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'placeholders' => [],
                'collections' => new ArrayCollection(),
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => [],
            ]
        );

        $data = $this->normalizer->normalize(new VersionedValue($block, 1));

        self::assertTrue($data['is_container']);
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\Serializer\Normalizer\V1\BlockNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new Value(), false],
            [new Block(), false],
            [new VersionedValue(new Value(), 1), false],
            [new VersionedValue(new Block(), 2), false],
            [new VersionedValue(new Block(), 1), true],
        ];
    }
}
