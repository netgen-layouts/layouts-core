<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs\NormalizerStub;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

final class BlockNormalizerTest extends TestCase
{
    private MockObject $normalizerMock;

    private MockObject $blockServiceMock;

    private BlockNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->normalizer = new BlockNormalizer($this->blockServiceMock);
        $this->normalizer->setNormalizer(new Serializer([new NormalizerStub()]));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer::buildValues
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer::getBlockCollections
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => Uuid::uuid4(),
                'status' => APIValue::STATUS_PUBLISHED,
                'offset' => 10,
                'limit' => 5,
            ],
        );

        $placeholder = Placeholder::fromArray(['identifier' => 'main']);

        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'layoutId' => Uuid::uuid4(),
                'definition' => BlockDefinition::fromArray(['identifier' => 'definition']),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'position' => 3,
                'status' => APIValue::STATUS_PUBLISHED,
                'placeholders' => [
                    'main' => $placeholder,
                ],
                'collections' => new ArrayCollection(
                    ['default' => $collection],
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
                        ],
                    ),
                ],
            ],
        );

        $this->blockServiceMock
            ->expects(self::once())
            ->method('hasPublishedState')
            ->with(self::identicalTo($block))
            ->willReturn(true);

        self::assertSame(
            [
                'id' => $block->getId()->toString(),
                'layout_id' => $block->getLayoutId()->toString(),
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
                        'collection_id' => $collection->getId()->toString(),
                        'collection_type' => Collection::TYPE_MANUAL,
                        'offset' => $collection->getOffset(),
                        'limit' => $collection->getLimit(),
                    ],
                ],
                'config' => ['config_key' => ['param' => 'data']],
            ],
            $this->normalizer->normalize(new Value($block)),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer::buildValues
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer::normalize
     */
    public function testNormalizeWithContainerBlock(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'layoutId' => Uuid::uuid4(),
                'definition' => ContainerDefinition::fromArray(
                    [
                        'identifier' => 'definition',
                        'handler' => new ContainerDefinitionHandler(),
                    ],
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'position' => 3,
                'status' => APIValue::STATUS_PUBLISHED,
                'placeholders' => [],
                'collections' => new ArrayCollection(),
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => [],
            ],
        );

        $data = $this->normalizer->normalize(new Value($block));

        self::assertTrue($data['is_container']);
    }

    /**
     * @param mixed $data
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer::supportsNormalization
     *
     * @dataProvider supportsNormalizationDataProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public static function supportsNormalizationDataProvider(): iterable
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
            [new Block(), false],
            [new Value(new APIValue()), false],
            [new Value(new Block()), true],
        ];
    }
}
