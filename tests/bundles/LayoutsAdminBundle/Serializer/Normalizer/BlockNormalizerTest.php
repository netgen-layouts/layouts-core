<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\BlockNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs\NormalizerStub;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Block\PlaceholderList;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigList;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Serializer;

#[CoversClass(BlockNormalizer::class)]
final class BlockNormalizerTest extends TestCase
{
    private MockObject&BlockService $blockServiceMock;

    private BlockNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->normalizer = new BlockNormalizer($this->blockServiceMock);
        $this->normalizer->setNormalizer(new Serializer([new NormalizerStub()]));
    }

    public function testNormalize(): void
    {
        $collection = Collection::fromArray(
            [
                'id' => Uuid::uuid4(),
                'status' => Status::Published,
                'offset' => 10,
                'limit' => 5,
                'query' => null,
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
                'status' => Status::Published,
                'placeholders' => new PlaceholderList(['main' => $placeholder]),
                'collections' => CollectionList::fromArray(['default' => $collection]),
                'isTranslatable' => true,
                'isAlwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => new ParameterList(['param' => new Parameter()]),
                'configs' => new ConfigList(
                    [
                        'config_key' => Config::fromArray(
                            [
                                'parameters' => new ParameterList(
                                    [
                                        'param' => new Parameter(),
                                    ],
                                ),
                            ],
                        ),
                    ],
                ),
            ],
        );

        $this->blockServiceMock
            ->expects($this->once())
            ->method('hasPublishedState')
            ->with(self::identicalTo($block))
            ->willReturn(true);

        self::assertSame(
            [
                'id' => $block->id->toString(),
                'layout_id' => $block->layoutId->toString(),
                'definition_identifier' => $block->definition->identifier,
                'name' => $block->name,
                'parent_position' => $block->position,
                'parameters' => ['param' => 'data'],
                'view_type' => $block->viewType,
                'item_view_type' => $block->itemViewType,
                'published' => true,
                'has_published_state' => true,
                'locale' => $block->locale,
                'is_translatable' => $block->isTranslatable,
                'always_available' => $block->isAlwaysAvailable,
                'is_container' => false,
                'placeholders' => ['data'],
                'collections' => [
                    [
                        'identifier' => 'default',
                        'collection_id' => $collection->id->toString(),
                        'collection_type' => 0,
                        'offset' => $collection->offset,
                        'limit' => $collection->limit,
                    ],
                ],
                'config' => ['config_key' => ['param' => 'data']],
            ],
            $this->normalizer->normalize(new Value($block)),
        );
    }

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
                'status' => Status::Published,
                'placeholders' => new PlaceholderList(),
                'collections' => CollectionList::fromArray([]),
                'isTranslatable' => true,
                'isAlwaysAvailable' => true,
                'availableLocales' => ['en'],
                'mainLocale' => 'en',
                'locale' => 'en',
                'parameters' => new ParameterList(),
                'configs' => new ConfigList(),
            ],
        );

        $data = $this->normalizer->normalize(new Value($block));

        self::assertTrue($data['is_container']);
    }

    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
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
