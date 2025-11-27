<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class LayoutServiceTestBase extends CoreTestCase
{
    use ExportObjectTrait;

    public function testLoadLayout(): void
    {
        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        self::assertTrue($layout->isPublished);
    }

    public function testLoadLayoutThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageMatches('/^Could not find layout with identifier "[\w-]+"$/');

        $this->layoutService->loadLayout(Uuid::uuid4());
    }

    public function testLoadLayoutDraft(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        self::assertTrue($layout->isDraft);
    }

    public function testLoadLayoutDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageMatches('/^Could not find layout with identifier "[\w-]+"$/');

        $this->layoutService->loadLayoutDraft(Uuid::uuid4());
    }

    public function testLoadLayoutArchive(): void
    {
        $layout = $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));

        self::assertTrue($layout->isArchived);
    }

    public function testLoadLayoutArchiveThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageMatches('/^Could not find layout with identifier "[\w-]+"$/');

        $this->layoutService->loadLayoutArchive(Uuid::uuid4());
    }

    public function testLoadLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts();

        self::assertCount(3, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->shared);
            self::assertTrue($layout->isPublished);
        }
    }

    public function testLoadLayoutsWithUnpublishedLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts(true);

        self::assertCount(5, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->shared);

            if (!$layout->isPublished) {
                try {
                    $this->layoutService->loadLayout($layout->id);
                    self::fail('Layout in draft status with existing published version loaded.');
                } catch (NotFoundException) {
                    // Do nothing
                }
            }
        }
    }

    public function testGetLayoutsCount(): void
    {
        self::assertSame(3, $this->layoutService->getLayoutsCount());
    }

    public function testGetLayoutsCountWithUnpublishedLayouts(): void
    {
        self::assertSame(5, $this->layoutService->getLayoutsCount(true));
    }

    public function testLoadSharedLayouts(): void
    {
        $layouts = $this->layoutService->loadSharedLayouts();

        self::assertCount(2, $layouts);

        foreach ($layouts as $layout) {
            self::assertTrue($layout->shared);
            self::assertTrue($layout->isPublished);
        }
    }

    public function testGetSharedLayoutsCount(): void
    {
        self::assertSame(2, $this->layoutService->getSharedLayoutsCount());
    }

    public function testLoadAllLayouts(): void
    {
        $layouts = $this->layoutService->loadAllLayouts();

        self::assertCount(5, $layouts);

        foreach ($layouts as $layout) {
            self::assertTrue($layout->isPublished);
        }
    }

    public function testGetAllLayoutsCount(): void
    {
        self::assertSame(5, $this->layoutService->getAllLayoutsCount());
    }

    public function testLoadRelatedLayouts(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $layouts = $this->layoutService->loadRelatedLayouts($sharedLayout);

        self::assertCount(1, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->shared);
            self::assertTrue($layout->isPublished);
        }
    }

    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Related layouts can only be loaded for published shared layouts.');

        $sharedLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Related layouts can only be loaded for shared layouts.');

        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    public function testGetRelatedLayoutsCount(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $count = $this->layoutService->getRelatedLayoutsCount($sharedLayout);

        self::assertSame(1, $count);
    }

    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Count of related layouts can only be loaded for published shared layouts.');

        $sharedLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Count of related layouts can only be loaded for shared layouts.');

        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    public function testLayoutExists(): void
    {
        self::assertTrue($this->layoutService->layoutExists(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')));
    }

    public function testLayoutExistsReturnsFalse(): void
    {
        self::assertFalse($this->layoutService->layoutExists(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff')));
    }

    public function testLayoutNameExists(): void
    {
        self::assertTrue($this->layoutService->layoutNameExists('My layout'));
    }

    public function testLayoutNameNotExistsWithExcludedLayoutId(): void
    {
        self::assertFalse($this->layoutService->layoutNameExists('My layout', Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')));
    }

    public function testLayoutNameNotExists(): void
    {
        self::assertFalse($this->layoutService->layoutNameExists('Non existing'));
    }

    public function testLinkZone(): void
    {
        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);

        $updatedZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');

        self::assertInstanceOf(Zone::class, $updatedZone->linkedZone);
        self::assertTrue($updatedZone->linkedZone->isPublished);
        self::assertSame($linkedZone->layoutId->toString(), $updatedZone->linkedZone->layoutId->toString());
        self::assertSame($linkedZone->identifier, $updatedZone->linkedZone->identifier);
    }

    public function testLinkZoneThrowsBadStateExceptionWhenInSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Zone cannot be in the shared layout.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('399ad9ac-777a-50ba-945a-06e9f57add12'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    public function testLinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Only draft zones can be linked.');

        $zone = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    public function testLinkZoneThrowsBadStateExceptionWithNonPublishedLinkedZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Zones can only be linked to published zones.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    public function testLinkZoneThrowsBadStateExceptionWhenLinkedZoneNotInSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    public function testLinkZoneThrowsBadStateExceptionWhenInTheSameLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Linked zone needs to be in a different layout.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    public function testUnlinkZone(): void
    {
        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        $this->layoutService->unlinkZone($zone);

        $updatedZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        self::assertNull($updatedZone->linkedZone);
    }

    public function testUnlinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Only draft zones can be unlinked.');

        $zone = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        $this->layoutService->unlinkZone($zone);
    }

    public function testCreateLayout(): void
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My new layout',
            'en',
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        self::assertTrue($createdLayout->isDraft);

        self::assertGreaterThan(new DateTimeImmutable('@0'), $createdLayout->created);

        self::assertSame(
            $createdLayout->created->format(DateTimeInterface::ATOM),
            $createdLayout->modified->format(DateTimeInterface::ATOM),
        );
    }

    public function testCreateLayoutWithCustomUuid(): void
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My new layout',
            'en',
        );

        $layoutCreateStruct->uuid = Uuid::fromString('5f35d4d3-8fa7-4602-9d4c-c74c2b16e3d7');

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        self::assertTrue($createdLayout->isDraft);
        self::assertGreaterThan(new DateTimeImmutable('@0'), $createdLayout->created);
        self::assertSame($layoutCreateStruct->uuid->toString(), $createdLayout->id->toString());

        self::assertSame(
            $createdLayout->created->format(DateTimeInterface::ATOM),
            $createdLayout->modified->format(DateTimeInterface::ATOM),
        );
    }

    public function testCreateLayoutWithExistingCustomUuidThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "uuid" has an invalid state. Layout with provided UUID already exists.');

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My new layout',
            'en',
        );

        $layoutCreateStruct->uuid = Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136');

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    public function testCreateLayoutThrowsBadStateExceptionOnExistingName(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "name" has an invalid state. Layout with provided name already exists.');

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My layout',
            'en',
        );

        $this->layoutService->createLayout($layoutCreateStruct);
    }

    public function testAddTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $updatedLayout = $this->layoutService->addTranslation($layout, 'de', 'en');

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        self::assertSame(['en', 'hr', 'de'], $updatedLayout->availableLocales);

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en', 'hr', 'de']);
        foreach ($layoutBlocks as $layoutBlock) {
            $layoutBlock->isTranslatable ?
                self::assertContains('de', $layoutBlock->availableLocales) :
                self::assertNotContains('de', $layoutBlock->availableLocales);

            $layoutBlock->isTranslatable ?
                self::assertContains('de', $layoutBlock->availableLocales) :
                self::assertNotContains('de', $layoutBlock->availableLocales);
        }
    }

    public function testAddTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only add translation to draft layouts.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->addTranslation($layout, 'de', 'en');
    }

    public function testAddTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout already has the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->addTranslation($layout, 'en', 'hr');
    }

    public function testSetMainTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $updatedLayout = $this->layoutService->setMainTranslation($layout, 'hr');

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        self::assertSame('hr', $updatedLayout->mainLocale);
        self::assertSame(['en', 'hr'], $updatedLayout->availableLocales);

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['hr', 'en']);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertSame('hr', $layoutBlock->mainLocale);
        }
    }

    public function testSetMainTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only set main translation in draft layouts.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->setMainTranslation($layout, 'hr');
    }

    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "mainLocale" has an invalid state. Layout does not have the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->setMainTranslation($layout, 'de');
    }

    public function testRemoveTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $updatedLayout = $this->layoutService->removeTranslation($layout, 'hr');
        self::assertNotContains('hr', $updatedLayout->availableLocales);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en']);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertNotContains('hr', $layoutBlock->availableLocales);
        }
    }

    public function testRemoveTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only remove translations from draft layouts.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->removeTranslation($layout, 'hr');
    }

    public function testRemoveTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout does not have the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->removeTranslation($layout, 'de');
    }

    public function testRemoveTranslationThrowsBadStateExceptionWithMainLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Main translation cannot be removed from the layout.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->removeTranslation($layout, 'en');
    }

    public function testUpdateLayout(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';
        $layoutUpdateStruct->description = 'New description';

        $updatedLayout = $this->layoutService->updateLayout($layout, $layoutUpdateStruct);

        self::assertTrue($updatedLayout->isDraft);
        self::assertSame('New name', $updatedLayout->name);
        self::assertSame('New description', $updatedLayout->description);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);
    }

    public function testUpdateLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only draft layouts can be updated.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout($layout, $layoutUpdateStruct);
    }

    public function testUpdateLayoutThrowsBadStateExceptionWithExistingLayoutName(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "name" has an invalid state. Layout with provided name already exists.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'My layout';

        $this->layoutService->updateLayout(
            $layout,
            $layoutUpdateStruct,
        );
    }

    public function testCopyLayout(): void
    {
        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'New name';
        $copyStruct->description = 'New description';

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        self::assertSame($layout->isPublished, $copiedLayout->isPublished);

        self::assertGreaterThan($layout->created, $copiedLayout->created);

        self::assertSame(
            $copiedLayout->created->format(DateTimeInterface::ATOM),
            $copiedLayout->modified->format(DateTimeInterface::ATOM),
        );

        self::assertNotSame($layout->id->toString(), $copiedLayout->id->toString());
        self::assertSame('New name', $copiedLayout->name);
        self::assertSame('New description', $copiedLayout->description);
    }

    public function testCopyLayoutThrowsBadStateExceptionOnExistingLayoutName(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.');

        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'My other layout';

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->copyLayout($layout, $copyStruct);
    }

    public function testChangeLayoutType(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            [
                'top' => ['left', 'right'],
            ],
        );

        self::assertSame($layout->id->toString(), $updatedLayout->id->toString());
        self::assertSame($layout->status, $updatedLayout->status);
        self::assertSame('4_zones_b', $updatedLayout->layoutType->identifier);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $topZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('top'),
        );

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('bottom'),
        );

        self::assertCount(3, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertInstanceOf(Block::class, $topZoneBlocks[0]);
        self::assertInstanceOf(Block::class, $topZoneBlocks[1]);
        self::assertInstanceOf(Block::class, $topZoneBlocks[2]);

        self::assertSame('b07d3a85-bcdb-5af2-9b6f-deba36c700e7', $topZoneBlocks[0]->id->toString());
        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $topZoneBlocks[1]->id->toString());
        self::assertSame('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e', $topZoneBlocks[2]->id->toString());
    }

    public function testChangeLayoutTypeWithSameLayoutType(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            [
                'top' => ['left', 'right'],
            ],
        );

        self::assertSame($layout->id->toString(), $updatedLayout->id->toString());
        self::assertSame($layout->status, $updatedLayout->status);
        self::assertSame('4_zones_a', $updatedLayout->layoutType->identifier);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $topZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('top'),
        );

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left'),
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('right'),
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('bottom'),
        );

        self::assertCount(3, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertInstanceOf(Block::class, $topZoneBlocks[0]);
        self::assertInstanceOf(Block::class, $topZoneBlocks[1]);
        self::assertInstanceOf(Block::class, $topZoneBlocks[2]);

        self::assertSame('b07d3a85-bcdb-5af2-9b6f-deba36c700e7', $topZoneBlocks[0]->id->toString());
        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $topZoneBlocks[1]->id->toString());
        self::assertSame('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e', $topZoneBlocks[2]->id->toString());
    }

    public function testChangeLayoutTypeWithSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            [
                'top' => ['top'],
            ],
        );

        self::assertSame($layout->id->toString(), $updatedLayout->id->toString());
        self::assertSame($layout->status, $updatedLayout->status);
        self::assertSame('4_zones_a', $updatedLayout->layoutType->identifier);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $topZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left'),
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('right'),
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('bottom'),
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertTrue($topZone->hasLinkedZone);

        $newTopZone = $layout->getZone('top');

        self::assertInstanceOf(Zone::class, $topZone->linkedZone);
        self::assertInstanceOf(Zone::class, $newTopZone->linkedZone);

        self::assertSame($newTopZone->linkedZone->layoutId->toString(), $topZone->linkedZone->layoutId->toString());
        self::assertSame($newTopZone->linkedZone->identifier, $topZone->linkedZone->identifier);
    }

    public function testChangeLayoutTypeWithSameLayoutTypeAndSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            [
                'top' => ['top'],
            ],
        );

        self::assertSame($layout->id->toString(), $updatedLayout->id->toString());
        self::assertSame($layout->status, $updatedLayout->status);
        self::assertSame('4_zones_b', $updatedLayout->layoutType->identifier);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $topZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left'),
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('right'),
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('bottom'),
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertTrue($topZone->hasLinkedZone);

        $newTopZone = $layout->getZone('top');

        self::assertInstanceOf(Zone::class, $topZone->linkedZone);
        self::assertInstanceOf(Zone::class, $newTopZone->linkedZone);

        self::assertSame($newTopZone->linkedZone->layoutId->toString(), $topZone->linkedZone->layoutId->toString());
        self::assertSame($newTopZone->linkedZone->identifier, $topZone->linkedZone->identifier);
    }

    public function testChangeLayoutTypeWithSharedZonesAndDiscardingSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            [
                'top' => ['top'],
            ],
            false,
        );

        self::assertSame($layout->id->toString(), $updatedLayout->id->toString());
        self::assertSame($layout->status, $updatedLayout->status);
        self::assertSame('4_zones_a', $updatedLayout->layoutType->identifier);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $topZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left'),
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('right'),
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('bottom'),
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertFalse($topZone->hasLinkedZone);
    }

    public function testChangeLayoutTypeWithSameLayoutTypeAndSharedZonesAndDiscardingSharedZones(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            [
                'top' => ['top'],
            ],
            false,
        );

        self::assertSame($layout->id->toString(), $updatedLayout->id->toString());
        self::assertSame($layout->status, $updatedLayout->status);
        self::assertSame('4_zones_b', $updatedLayout->layoutType->identifier);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $updatedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $topZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');
        $topZoneBlocks = $this->blockService->loadZoneBlocks($topZone);

        $leftZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left'),
        );

        $rightZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('right'),
        );

        $bottomZoneBlocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('bottom'),
        );

        self::assertCount(0, $topZoneBlocks);
        self::assertCount(0, $leftZoneBlocks);
        self::assertCount(0, $rightZoneBlocks);
        self::assertCount(0, $bottomZoneBlocks);

        self::assertFalse($topZone->hasLinkedZone);
    }

    public function testChangeLayoutTypeThrowsBadStateExceptionOnNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Layout type can only be changed for draft layouts.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->changeLayoutType(
            $layout,
            $this->layoutTypeRegistry->getLayoutType('4_zones_b'),
            [],
        );
    }

    public function testCreateDraft(): void
    {
        $layout = $this->layoutService->loadLayout(Uuid::fromString('7900306c-0351-5f0a-9b33-5d4f5a1f3943'));
        $draftLayout = $this->layoutService->createDraft($layout);

        self::assertTrue($draftLayout->isDraft);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $draftLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $draftLayout->modified);
    }

    public function testCreateDraftWithDiscardingExistingDraft(): void
    {
        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $draftLayout = $this->layoutService->createDraft($layout, true);

        self::assertTrue($draftLayout->isDraft);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $draftLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $draftLayout->modified);
    }

    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Drafts can only be created from published layouts.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $this->layoutService->createDraft($layout);
    }

    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. The provided layout already has a draft.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->createDraft($layout);
    }

    public function testDiscardDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->discardDraft($layout);

        $this->layoutService->loadLayoutDraft($layout->id);
    }

    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only drafts can be discarded.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->discardDraft($layout);
    }

    public function testPublishLayout(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $currentlyPublishedLayout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $publishedLayout = $this->layoutService->publishLayout($layout);

        self::assertTrue($publishedLayout->isPublished);

        self::assertSame(
            $layout->created->format(DateTimeInterface::ATOM),
            $publishedLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->modified, $publishedLayout->modified);

        $archivedLayout = $this->layoutService->loadLayoutArchive(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        self::assertSame(
            $currentlyPublishedLayout->modified->format(DateTimeInterface::ATOM),
            $archivedLayout->modified->format(DateTimeInterface::ATOM),
        );

        try {
            $this->layoutService->loadLayoutDraft($layout->id);
            self::fail('Draft layout still exists after publishing.');
        } catch (NotFoundException) {
            // Do nothing
        }
    }

    public function testPublishLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only drafts can be published.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->publishLayout($layout);
    }

    public function testRestoreFromArchive(): void
    {
        $originalLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $publishedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $restoredLayout = $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );

        self::assertTrue($restoredLayout->isDraft);
        self::assertSame($publishedLayout->name, $restoredLayout->name);

        self::assertSame(
            $originalLayout->created->format(DateTimeInterface::ATOM),
            $restoredLayout->created->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($originalLayout->modified, $restoredLayout->modified);
    }

    public function testRestoreFromArchiveWithoutDraft(): void
    {
        $originalLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $this->layoutService->discardDraft($originalLayout);

        $publishedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $restoredLayout = $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );

        self::assertTrue($restoredLayout->isDraft);
        self::assertSame($publishedLayout->name, $restoredLayout->name);
    }

    public function testRestoreFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Only archived layouts can be restored.');

        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );
    }

    public function testRestoreFromArchiveThrowsNotFoundExceptionOnNonExistingPublishedVersion(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "71cbe281-430c-51d5-8e21-c3cc4e656dac"');

        $this->layoutHandler->deleteLayout(2, PersistenceStatus::Published);
        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );
    }

    public function testDeleteLayout(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->deleteLayout($layout);

        $this->layoutService->loadLayout($layout->id);
    }

    public function testNewLayoutCreateStruct(): void
    {
        $layoutType = LayoutType::fromArray(['identifier' => '4_zones_a']);

        $struct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            'New layout',
            'en',
        );

        self::assertSame(
            [
                'description' => '',
                'layoutType' => $layoutType,
                'mainLocale' => 'en',
                'name' => 'New layout',
                'shared' => false,
                'uuid' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewLayoutUpdateStruct(): void
    {
        $struct = $this->layoutService->newLayoutUpdateStruct(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertSame(
            [
                'description' => 'My layout description',
                'name' => 'My layout',
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewLayoutUpdateStructWithNoLayout(): void
    {
        $struct = $this->layoutService->newLayoutUpdateStruct();

        self::assertSame(
            [
                'description' => null,
                'name' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewLayoutCopyStruct(): void
    {
        $struct = $this->layoutService->newLayoutCopyStruct(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertSame(
            [
                'description' => null,
                'name' => 'My layout (copy)',
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewLayoutCopyStructWithNoLayout(): void
    {
        $struct = $this->layoutService->newLayoutCopyStruct();

        self::assertSame(
            [
                'description' => null,
                'name' => 'Layout (copy)',
            ],
            $this->exportObject($struct),
        );
    }
}
