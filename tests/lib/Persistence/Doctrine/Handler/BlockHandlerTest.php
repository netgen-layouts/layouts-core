<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Handler;

use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Doctrine\Handler\BlockHandler;
use Netgen\Layouts\Persistence\Doctrine\Handler\CollectionHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\Layouts\Persistence\Handler\BlockHandlerInterface;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\BlockCreateStruct;
use Netgen\Layouts\Persistence\Values\Block\BlockTranslationUpdateStruct;
use Netgen\Layouts\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Persistence\Values\Status;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(BlockHandler::class)]
#[CoversClass(CollectionHandler::class)]
#[CoversClass(BlockQueryHandler::class)]
#[CoversClass(CollectionQueryHandler::class)]
final class BlockHandlerTest extends TestCase
{
    use ExportObjectTrait;
    use TestCaseTrait;
    use UuidGeneratorTrait;

    private BlockHandlerInterface $blockHandler;

    private LayoutHandlerInterface $layoutHandler;

    private CollectionHandlerInterface $collectionHandler;

    protected function setUp(): void
    {
        $this->createDatabase();

        $this->blockHandler = $this->createBlockHandler();
        $this->layoutHandler = $this->createLayoutHandler();
        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    protected function tearDown(): void
    {
        $this->closeDatabase();
    }

    public function testLoadBlock(): void
    {
        $block = $this->blockHandler->loadBlock(31, Status::Published);

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard_with_intro',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My published block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                    'hr' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Published,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'grid',
            ],
            $this->exportObject($block),
        );
    }

    public function testLoadBlockThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "999"');

        $this->blockHandler->loadBlock(999, Status::Published);
    }

    public function testBlockExists(): void
    {
        self::assertTrue($this->blockHandler->blockExists(31, Status::Published));
    }

    public function testBlockNotExists(): void
    {
        self::assertFalse($this->blockHandler->blockExists(999, Status::Published));
    }

    public function testBlockNotExistsInStatus(): void
    {
        self::assertFalse($this->blockHandler->blockExists(36, Status::Published));
    }

    public function testLoadLayoutBlocks(): void
    {
        $blocks = $this->blockHandler->loadLayoutBlocks(
            $this->layoutHandler->loadLayout(1, Status::Published),
        );

        self::assertCount(7, $blocks);
    }

    public function testLoadChildBlocks(): void
    {
        $blocks = $this->blockHandler->loadChildBlocks(
            $this->blockHandler->loadBlock(3, Status::Published),
        );

        self::assertContainsOnlyInstancesOf(Block::class, $blocks);

        self::assertSame(
            [
                [
                    'availableLocales' => ['en', 'hr'],
                    'config' => [],
                    'definitionIdentifier' => 'list',
                    'depth' => 1,
                    'id' => 31,
                    'isAlwaysAvailable' => true,
                    'isTranslatable' => true,
                    'itemViewType' => 'standard_with_intro',
                    'layoutId' => 1,
                    'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                    'mainLocale' => 'en',
                    'name' => 'My published block',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'parentId' => 3,
                    'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                    'path' => '/3/31/',
                    'placeholder' => 'root',
                    'position' => 0,
                    'status' => Status::Published,
                    'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                    'viewType' => 'grid',
                ],
                [
                    'availableLocales' => ['en'],
                    'config' => [],
                    'definitionIdentifier' => 'list',
                    'depth' => 1,
                    'id' => 35,
                    'isAlwaysAvailable' => true,
                    'isTranslatable' => false,
                    'itemViewType' => 'standard',
                    'layoutId' => 1,
                    'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                    'mainLocale' => 'en',
                    'name' => 'My fourth block',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'parentId' => 3,
                    'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                    'path' => '/3/35/',
                    'placeholder' => 'root',
                    'position' => 1,
                    'status' => Status::Published,
                    'uuid' => 'c2a30ea3-95ef-55b0-a584-fbcfd93cec9e',
                    'viewType' => 'grid',
                ],
            ],
            $this->exportObjectList($blocks),
        );
    }

    public function testLoadChildBlocksInPlaceholder(): void
    {
        $blocks = $this->blockHandler->loadChildBlocks(
            $this->blockHandler->loadBlock(33, Status::Draft),
            'left',
        );

        self::assertContainsOnlyInstancesOf(Block::class, $blocks);

        self::assertSame(
            [
                [
                    'availableLocales' => ['en'],
                    'config' => [],
                    'definitionIdentifier' => 'text',
                    'depth' => 2,
                    'id' => 37,
                    'isAlwaysAvailable' => true,
                    'isTranslatable' => false,
                    'itemViewType' => 'standard',
                    'layoutId' => 2,
                    'layoutUuid' => '71cbe281-430c-51d5-8e21-c3cc4e656dac',
                    'mainLocale' => 'en',
                    'name' => 'My seventh block',
                    'parameters' => [
                        'en' => [
                            'content' => 'Text',
                        ],
                    ],
                    'parentId' => 33,
                    'parentUuid' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                    'path' => '/7/33/37/',
                    'placeholder' => 'left',
                    'position' => 0,
                    'status' => Status::Draft,
                    'uuid' => '129f51de-a535-5094-8517-45d672e06302',
                    'viewType' => 'text',
                ],
            ],
            $this->exportObjectList($blocks),
        );
    }

    public function testLoadChildBlocksWithUnknownPlaceholder(): void
    {
        self::assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(33, Status::Draft),
                'unknown',
            ),
        );
    }

    public function testCreateBlock(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->isAlwaysAvailable = true;
        $blockCreateStruct->status = Status::Draft;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $blockCreateStruct->config = [
            'config_key' => [
                'config_param' => 'Config value',
            ],
        ];

        $createdBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Status::Draft),
                $this->blockHandler->loadBlock(3, Status::Draft),
                'root',
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [
                    'config_key' => [
                        'config_param' => 'Config value',
                    ],
                ],
                'definitionIdentifier' => 'new_block',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'a_param' => 'A value',
                    ],
                    'hr' => [
                        'a_param' => 'A value',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/39/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'large',
            ],
            $this->exportObject($createdBlock),
        );

        $secondBlock = $this->blockHandler->loadBlock(31, Status::Draft);
        self::assertSame(1, $secondBlock->position);
    }

    public function testCreateBlockTranslation(): void
    {
        $block = $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'de',
            'en',
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr', 'de'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                    'de' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'list',
            ],
            $this->exportObject($block),
        );
    }

    public function testCreateBlockTranslationWithNonMainSourceLocale(): void
    {
        $block = $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'de',
            'hr',
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr', 'de'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                    'de' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'list',
            ],
            $this->exportObject($block),
        );
    }

    public function testCreateBlockTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Block already has the provided locale.');

        $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'en',
            'hr',
        );
    }

    public function testCreateBlockTranslationThrowsBadStateExceptionWithNonExistingSourceLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Block does not have the provided source locale.');

        $this->blockHandler->createBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'de',
            'fr',
        );
    }

    public function testCreateBlockWithNoParent(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = false;
        $blockCreateStruct->isAlwaysAvailable = true;
        $blockCreateStruct->status = Status::Draft;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $blockCreateStruct->config = [
            'config_key' => [
                'config_param' => 'Config value',
            ],
        ];

        $block = $this->withUuids(
            fn (): Block => $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Status::Draft),
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [
                    'config_key' => [
                        'config_param' => 'Config value',
                    ],
                ],
                'definitionIdentifier' => 'new_block',
                'depth' => 0,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'a_param' => 'A value',
                    ],
                ],
                'parentId' => null,
                'parentUuid' => null,
                'path' => '/39/',
                'placeholder' => null,
                'position' => null,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'large',
            ],
            $this->exportObject($block),
        );
    }

    public function testCreateBlockWithNoPosition(): void
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->position = null;
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->isAlwaysAvailable = true;
        $blockCreateStruct->status = Status::Draft;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $blockCreateStruct->config = [
            'config_key' => [
                'config_param' => 'Config value',
            ],
        ];

        $block = $this->withUuids(
            fn (): Block => $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Status::Draft),
                $this->blockHandler->loadBlock(3, Status::Draft),
                'root',
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [
                    'config_key' => [
                        'config_param' => 'Config value',
                    ],
                ],
                'definitionIdentifier' => 'new_block',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'a_param' => 'A value',
                    ],
                    'hr' => [
                        'a_param' => 'A value',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/39/',
                'placeholder' => 'root',
                'position' => 2,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'large',
            ],
            $this->exportObject($block),
        );
    }

    public function testCreateBlockThrowsBadStateExceptionOnTargetBlockInDifferentLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Target block is not in the provided layout.');

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->isAlwaysAvailable = true;
        $blockCreateStruct->status = Status::Draft;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 0;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Status::Draft),
            $this->blockHandler->loadBlock(5, Status::Draft),
            'root',
        );
    }

    public function testCreateBlockThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position cannot be negative.');

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->isAlwaysAvailable = true;
        $blockCreateStruct->status = Status::Draft;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = -5;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->config = [];
        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Status::Draft),
            $this->blockHandler->loadBlock(3, Status::Draft),
            'root',
        );
    }

    public function testCreateBlockThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->isTranslatable = true;
        $blockCreateStruct->isAlwaysAvailable = true;
        $blockCreateStruct->status = Status::Draft;
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->position = 9999;
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->config = [];
        $blockCreateStruct->parameters = [
            'a_param' => 'A value',
        ];

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Status::Draft),
            $this->blockHandler->loadBlock(3, Status::Draft),
            'root',
        );
    }

    public function testUpdateBlock(): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->itemViewType = 'new';
        $blockUpdateStruct->name = 'Updated name';
        $blockUpdateStruct->isTranslatable = false;
        $blockUpdateStruct->isAlwaysAvailable = false;
        $blockUpdateStruct->config = [
            'config_key' => [
                'config_param' => 'Config value',
            ],
        ];

        $updatedBlock = $this->blockHandler->updateBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
            $blockUpdateStruct,
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [
                    'config_key' => [
                        'config_param' => 'Config value',
                    ],
                ],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => false,
                'isTranslatable' => false,
                'itemViewType' => 'new',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'Updated name',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'large',
            ],
            $this->exportObject($updatedBlock),
        );
    }

    public function testUpdateBlockWithDefaultValues(): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();

        $updatedBlock = $this->blockHandler->updateBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
            $blockUpdateStruct,
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'list',
            ],
            $this->exportObject($updatedBlock),
        );
    }

    public function testUpdateBlockTranslation(): void
    {
        $translationUpdateStruct = new BlockTranslationUpdateStruct();

        $translationUpdateStruct->parameters = [
            'number_of_columns' => 4,
            'some_param' => 'Some value',
        ];

        $updatedBlock = $this->blockHandler->updateBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'en',
            $translationUpdateStruct,
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 4,
                        'some_param' => 'Some value',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'list',
            ],
            $this->exportObject($updatedBlock),
        );
    }

    public function testUpdateBlockTranslationWithDefaultValues(): void
    {
        $translationUpdateStruct = new BlockTranslationUpdateStruct();

        $updatedBlock = $this->blockHandler->updateBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'en',
            $translationUpdateStruct,
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'list',
            ],
            $this->exportObject($updatedBlock),
        );
    }

    public function testUpdateBlockTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Block does not have the provided locale.');

        $this->blockHandler->updateBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'de',
            new BlockTranslationUpdateStruct(),
        );
    }

    public function testSetMainTranslation(): void
    {
        $block = $this->blockHandler->loadBlock(31, Status::Draft);
        $block = $this->blockHandler->setMainTranslation($block, 'hr');

        self::assertSame('hr', $block->mainLocale);
    }

    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "mainLocale" has an invalid state. Block does not have the provided locale.');

        $block = $this->blockHandler->loadBlock(31, Status::Draft);
        $this->blockHandler->setMainTranslation($block, 'de');
    }

    public function testCopyBlock(): void
    {
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(31, Status::Draft),
                $this->blockHandler->loadBlock(3, Status::Draft),
                'root',
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'cf29cf92-0294-5581-abdb-58d11978186b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/39/',
                'placeholder' => 'root',
                'position' => 2,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'list',
            ],
            $this->exportObject($copiedBlock),
        );

        self::assertSame(
            [
                [
                    'blockId' => 39,
                    'blockStatus' => Status::Draft,
                    'collectionId' => 7,
                    'collectionStatus' => Status::Draft,
                    'identifier' => 'default',
                ],
                [
                    'blockId' => 39,
                    'blockStatus' => Status::Draft,
                    'collectionId' => 8,
                    'collectionStatus' => Status::Draft,
                    'identifier' => 'featured',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionReferences($copiedBlock),
            ),
        );
    }

    public function testCopyBlockWithPosition(): void
    {
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(31, Status::Draft),
                $this->blockHandler->loadBlock(3, Status::Draft),
                'root',
                1,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'cf29cf92-0294-5581-abdb-58d11978186b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/39/',
                'placeholder' => 'root',
                'position' => 1,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'list',
            ],
            $this->exportObject($copiedBlock),
        );
    }

    public function testCopyBlockWithSamePosition(): void
    {
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(31, Status::Draft),
                $this->blockHandler->loadBlock(3, Status::Draft),
                'root',
                0,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'cf29cf92-0294-5581-abdb-58d11978186b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/39/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'list',
            ],
            $this->exportObject($copiedBlock),
        );
    }

    public function testCopyBlockWithLastPosition(): void
    {
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(31, Status::Draft),
                $this->blockHandler->loadBlock(3, Status::Draft),
                'root',
                2,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'cf29cf92-0294-5581-abdb-58d11978186b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/39/',
                'placeholder' => 'root',
                'position' => 2,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'list',
            ],
            $this->exportObject($copiedBlock),
        );
    }

    public function testCopyBlockWithLowerPosition(): void
    {
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(35, Status::Draft),
                $this->blockHandler->loadBlock(3, Status::Draft),
                'root',
                0,
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'cf29cf92-0294-5581-abdb-58d11978186b',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
            ],
        );

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My fourth block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/39/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'grid',
            ],
            $this->exportObject($copiedBlock),
        );
    }

    public function testCopyBlockThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position cannot be negative.');

        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
            $this->blockHandler->loadBlock(3, Status::Draft),
            'root',
            -1,
        );
    }

    public function testCopyBlockThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
            $this->blockHandler->loadBlock(3, Status::Draft),
            'root',
            9999,
        );
    }

    public function testCopyBlockWithChildBlocks(): void
    {
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(33, Status::Draft),
                $this->blockHandler->loadBlock(7, Status::Draft),
                'root',
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
            ],
        );

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [],
                'definitionIdentifier' => 'two_columns',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 2,
                'layoutUuid' => '71cbe281-430c-51d5-8e21-c3cc4e656dac',
                'mainLocale' => 'en',
                'name' => 'My third block',
                'parameters' => [
                    'en' => [],
                ],
                'parentId' => 7,
                'parentUuid' => '8c4a5851-f2e0-5b46-a726-25230b5a3b9b',
                'path' => '/7/39/',
                'placeholder' => 'root',
                'position' => 3,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'two_columns_50_50',
            ],
            $this->exportObject($copiedBlock),
        );

        $copiedSubBlock = $this->blockHandler->loadBlock(40, Status::Draft);

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [],
                'definitionIdentifier' => 'text',
                'depth' => 2,
                'id' => 40,
                'isAlwaysAvailable' => true,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'layoutId' => 2,
                'layoutUuid' => '71cbe281-430c-51d5-8e21-c3cc4e656dac',
                'mainLocale' => 'en',
                'name' => 'My seventh block',
                'parameters' => [
                    'en' => [
                        'content' => 'Text',
                    ],
                ],
                'parentId' => 39,
                'parentUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'path' => '/7/39/40/',
                'placeholder' => 'left',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'viewType' => 'text',
            ],
            $this->exportObject($copiedSubBlock),
        );

        self::assertSame(
            [
                [
                    'blockId' => 40,
                    'blockStatus' => Status::Draft,
                    'collectionId' => 7,
                    'collectionStatus' => Status::Draft,
                    'identifier' => 'default',
                ],
            ],
            $this->exportObjectList(
                $this->collectionHandler->loadCollectionReferences($copiedSubBlock),
            ),
        );
    }

    public function testCopyBlockToBlockInDifferentLayout(): void
    {
        $copiedBlock = $this->withUuids(
            fn (): Block => $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(31, Status::Draft),
                $this->blockHandler->loadBlock(8, Status::Draft),
                'root',
            ),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'cf29cf92-0294-5581-abdb-58d11978186b',
                '805895b2-6292-5243-a0c0-06a6ec0e28a2',
                '76b05000-33ac-53f7-adfd-c91936d1f6b1',
                '6dc13cc7-fd76-5e41-8b0c-1ed93ece7fcf',
                '70fe4f3a-7e9d-5a1f-9e6a-b038c06ea117',
                '3a3aa59a-76fe-532f-8a03-c04a93d803f6',
                'f08717e5-5910-574d-b976-03d877c4729b',
                'e804ebd6-dc99-53bb-85d5-196d68933761',
                '910f4fe2-97b0-5599-8a45-8fb8a8e0ca6d',
                '8634280c-f498-416e-b4a7-0b0bd0869c85',
                '63326bc3-baee-49c9-82e7-7b2a9aca081a',
                '3a17132d-9072-45f3-a0b3-b91bd4b0fcf3',
                '29f091e0-81cc-4bd3-aec5-673cd06abce5',
            ],
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 39,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 2,
                'layoutUuid' => '71cbe281-430c-51d5-8e21-c3cc4e656dac',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 8,
                'parentUuid' => '842b223f-3d9c-58a3-97fd-d610a4479224',
                'path' => '/8/39/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'list',
            ],
            $this->exportObject($copiedBlock),
        );

        self::assertSame(
            [
                [
                    'blockId' => 39,
                    'blockStatus' => Status::Draft,
                    'collectionId' => 7,
                    'collectionStatus' => Status::Draft,
                    'identifier' => 'default',
                ],
                [
                    'blockId' => 39,
                    'blockStatus' => Status::Draft,
                    'collectionId' => 8,
                    'collectionStatus' => Status::Draft,
                    'identifier' => 'featured',
                ],
            ],
            $this->exportObjectList($this->collectionHandler->loadCollectionReferences($copiedBlock)),
        );
    }

    public function testCopyBlockBelowSelf(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Block cannot be copied below itself or its children.');

        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(33, Status::Draft),
            $this->blockHandler->loadBlock(33, Status::Draft),
            'main',
        );
    }

    public function testCopyBlockBelowChildren(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Block cannot be copied below itself or its children.');

        $this->blockHandler->copyBlock(
            $this->blockHandler->loadBlock(33, Status::Draft),
            $this->blockHandler->loadBlock(37, Status::Draft),
            'main',
        );
    }

    public function testMoveBlock(): void
    {
        $movedBlock = $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Status::Draft),
            $this->blockHandler->loadBlock(4, Status::Draft),
            'root',
            0,
        );

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [],
                'definitionIdentifier' => 'two_columns',
                'depth' => 1,
                'id' => 33,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My third block',
                'parameters' => [
                    'en' => [],
                ],
                'parentId' => 4,
                'parentUuid' => 'eaffe0e7-2cce-58c4-b1ca-ae29f7de61d1',
                'path' => '/4/33/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'viewType' => 'two_columns_50_50',
            ],
            $this->exportObject($movedBlock),
        );

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [],
                'definitionIdentifier' => 'text',
                'depth' => 2,
                'id' => 37,
                'isAlwaysAvailable' => true,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My seventh block',
                'parameters' => [
                    'en' => [
                        'content' => 'Text',
                    ],
                ],
                'parentId' => 33,
                'parentUuid' => 'e666109d-f1db-5fd5-97fa-346f50e9ae59',
                'path' => '/4/33/37/',
                'placeholder' => 'left',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '129f51de-a535-5094-8517-45d672e06302',
                'viewType' => 'text',
            ],
            $this->exportObject($this->blockHandler->loadBlock(37, Status::Draft)),
        );
    }

    public function testMoveBlockThrowsBadStateExceptionOnMovingToSamePlace(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Block is already in specified target block and placeholder.');

        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Status::Draft),
            $this->blockHandler->loadBlock(7, Status::Draft),
            'root',
            0,
        );
    }

    public function testMoveBlockThrowsBadStateExceptionOnMovingToSelf(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Block cannot be moved below itself or its children.');

        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Status::Draft),
            $this->blockHandler->loadBlock(33, Status::Draft),
            'main',
            0,
        );
    }

    public function testMoveBlockThrowsBadStateExceptionOnMovingToChildren(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetBlock" has an invalid state. Block cannot be moved below itself or its children.');

        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(33, Status::Draft),
            $this->blockHandler->loadBlock(37, Status::Draft),
            'main',
            0,
        );
    }

    public function testMoveBlockThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position cannot be negative.');

        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
            $this->blockHandler->loadBlock(4, Status::Draft),
            'root',
            -1,
        );
    }

    public function testMoveBlockThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
            $this->blockHandler->loadBlock(4, Status::Draft),
            'root',
            9999,
        );
    }

    public function testMoveBlockToPosition(): void
    {
        $movedBlock = $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(31, Status::Draft),
            1,
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                    'hr' => [
                        'css_class' => 'css-class-hr',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 1,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'list',
            ],
            $this->exportObject($movedBlock),
        );

        $firstBlock = $this->blockHandler->loadBlock(32, Status::Draft);
        self::assertSame(0, $firstBlock->position);
    }

    public function testMoveBlockToLowerPosition(): void
    {
        $movedBlock = $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(35, Status::Draft),
            0,
        );

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 35,
                'isAlwaysAvailable' => true,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My fourth block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/35/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => 'c2a30ea3-95ef-55b0-a584-fbcfd93cec9e',
                'viewType' => 'grid',
            ],
            $this->exportObject($movedBlock),
        );

        $firstBlock = $this->blockHandler->loadBlock(31, Status::Draft);
        self::assertSame(1, $firstBlock->position);
    }

    public function testMoveBlockThrowsBadStateExceptionOnMovingRootBlock(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Root blocks cannot be moved.');

        $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(1, Status::Draft),
            1,
        );
    }

    public function testMoveBlockToPositionThrowsBadStateExceptionOnNegativePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position cannot be negative.');

        $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(31, Status::Draft),
            -1,
        );
    }

    public function testMoveBlockToPositionThrowsBadStateExceptionOnTooLargePosition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "position" has an invalid state. Position is out of range.');

        $this->blockHandler->moveBlockToPosition(
            $this->blockHandler->loadBlock(31, Status::Draft),
            9999,
        );
    }

    public function testCreateBlockStatus(): void
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
        );

        $block = $this->blockHandler->createBlockStatus(
            $this->blockHandler->loadBlock(31, Status::Published),
            Status::Draft,
        );

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard_with_intro',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My published block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                    'hr' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'grid',
            ],
            $this->exportObject($block),
        );

        $collectionReferences = $this->collectionHandler->loadCollectionReferences($block);

        self::assertCount(2, $collectionReferences);

        $collectionIds = [
            $collectionReferences[0]->collectionId,
            $collectionReferences[1]->collectionId,
        ];

        self::assertContains(2, $collectionIds);
        self::assertContains(3, $collectionIds);
    }

    public function testRestoreBlock(): void
    {
        $block = $this->blockHandler->loadBlock(31, Status::Draft);

        $movedBlock = $this->blockHandler->moveBlock(
            $block,
            $this->blockHandler->loadBlock(2, Status::Draft),
            'root',
            1,
        );

        $restoredBlock = $this->blockHandler->restoreBlock($movedBlock, Status::Published);

        self::assertSame(
            [
                'availableLocales' => ['en', 'hr'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard_with_intro',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My published block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 3,
                    ],
                    'hr' => [
                        'number_of_columns' => 3,
                    ],
                ],
                'parentId' => 2,
                'parentUuid' => '39d3ab66-1589-540f-95c4-6381acb4f010',
                'path' => '/2/31/',
                'placeholder' => 'root',
                'position' => 1,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'grid',
            ],
            $this->exportObject($restoredBlock),
        );
    }

    public function testRestoreBlockThrowsBadStateExceptionWithSameState(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "block" has an invalid state. Block is already in provided status.');

        $block = $this->blockHandler->loadBlock(31, Status::Draft);

        $this->blockHandler->restoreBlock($block, Status::Draft);
    }

    public function testDeleteBlock(): void
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(31, Status::Draft),
        );

        $secondBlock = $this->blockHandler->loadBlock(32, Status::Draft);
        self::assertSame(0, $secondBlock->position);

        try {
            $this->blockHandler->loadBlock(31, Status::Draft);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException) {
            // Do nothing
        }

        try {
            $this->collectionHandler->loadCollection(1, Status::Draft);
            self::fail('Collection still exists after deleting a block.');
        } catch (NotFoundException) {
            // Do nothing
        }

        // Verify that shared collection still exists
        $this->collectionHandler->loadCollection(3, Status::Published);
    }

    #[DoesNotPerformAssertions]
    public function testDeleteBlockWithSubBlocks(): void
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(33, Status::Draft),
        );

        try {
            $this->blockHandler->loadBlock(33, Status::Draft);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(37, Status::Draft);
            self::fail('Sub-block still exists after deleting');
        } catch (NotFoundException) {
            // Do nothing
        }

        try {
            $this->collectionHandler->loadCollection(6, Status::Draft);
            self::fail('Collection still exists after deleting a sub-block.');
        } catch (NotFoundException) {
            // Do nothing
        }
    }

    public function testDeleteBlockTranslation(): void
    {
        $block = $this->blockHandler->deleteBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'hr',
        );

        self::assertSame(
            [
                'availableLocales' => ['en'],
                'config' => [],
                'definitionIdentifier' => 'list',
                'depth' => 1,
                'id' => 31,
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'number_of_columns' => 2,
                        'css_class' => 'css-class',
                        'css_id' => 'css-id',
                    ],
                ],
                'parentId' => 3,
                'parentUuid' => '96c7f078-a430-5a82-8d19-107182fb463f',
                'path' => '/3/31/',
                'placeholder' => 'root',
                'position' => 0,
                'status' => Status::Draft,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'viewType' => 'list',
            ],
            $this->exportObject($block),
        );
    }

    public function testDeleteBlockTranslationWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Block does not have the provided locale.');

        $this->blockHandler->deleteBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'de',
        );
    }

    public function testDeleteBlockTranslationWithMainLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Main translation cannot be removed from the block.');

        $this->blockHandler->deleteBlockTranslation(
            $this->blockHandler->loadBlock(31, Status::Draft),
            'en',
        );
    }

    public function testDeleteLayoutBlocks(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Status::Draft);

        // First we need to delete all zones to correctly delete the blocks
        $query = $this->databaseConnection->createQueryBuilder();
        $query->delete('nglayouts_zone')
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('status', ':status'),
                ),
            )
            ->setParameter('layout_id', $layout->id, Types::INTEGER)
            ->setParameter('status', $layout->status->value, Types::INTEGER);

        $query->executeStatement();

        $this->blockHandler->deleteLayoutBlocks($layout->id, $layout->status);

        self::assertEmpty($this->blockHandler->loadLayoutBlocks($layout));
    }

    #[DoesNotPerformAssertions]
    public function testDeleteBlocks(): void
    {
        $this->blockHandler->deleteBlocks([31, 32]);

        try {
            $this->blockHandler->loadBlock(31, Status::Draft);
            self::fail(
                sprintf(
                    'Draft block %d still available after deleting',
                    31,
                ),
            );
        } catch (NotFoundException) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(32, Status::Draft);
            self::fail(
                sprintf(
                    'Draft block %d still available after deleting',
                    32,
                ),
            );
        } catch (NotFoundException) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(31, Status::Published);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    31,
                ),
            );
        } catch (NotFoundException) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(32, Status::Published);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    32,
                ),
            );
        } catch (NotFoundException) {
            // Do nothing
        }
    }

    public function testDeleteBlocksInStatus(): void
    {
        $this->blockHandler->deleteBlocks([31, 32], Status::Published);

        $this->blockHandler->loadBlock(31, Status::Draft);
        $this->blockHandler->loadBlock(32, Status::Draft);

        try {
            $this->blockHandler->loadBlock(31, Status::Published);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    31,
                ),
            );
        } catch (NotFoundException) {
            // Do nothing
        }

        try {
            $this->blockHandler->loadBlock(32, Status::Published);
            self::fail(
                sprintf(
                    'Published block %d still available after deleting',
                    32,
                ),
            );
        } catch (NotFoundException) {
            // Do nothing
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }
}
