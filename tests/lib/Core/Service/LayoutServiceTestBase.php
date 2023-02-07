<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class LayoutServiceTestBase extends CoreTestCase
{
    use ExportObjectTrait;

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::__construct
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayout(): void
    {
        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        self::assertTrue($layout->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayout
     */
    public function testLoadLayoutThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageMatches('/^Could not find layout with identifier "[\w-]+"$/');

        $this->layoutService->loadLayout(Uuid::uuid4());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::__construct
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayoutDraft
     */
    public function testLoadLayoutDraft(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        self::assertTrue($layout->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayoutDraft
     */
    public function testLoadLayoutDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageMatches('/^Could not find layout with identifier "[\w-]+"$/');

        $this->layoutService->loadLayoutDraft(Uuid::uuid4());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::__construct
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayoutArchive
     */
    public function testLoadLayoutArchive(): void
    {
        $layout = $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));

        self::assertTrue($layout->isArchived());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayoutArchive
     */
    public function testLoadLayoutArchiveThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageMatches('/^Could not find layout with identifier "[\w-]+"$/');

        $this->layoutService->loadLayoutArchive(Uuid::uuid4());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts();

        self::assertCount(3, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->isShared());
            self::assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadLayouts
     */
    public function testLoadLayoutsWithUnpublishedLayouts(): void
    {
        $layouts = $this->layoutService->loadLayouts(true);

        self::assertCount(5, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->isShared());

            if (!$layout->isPublished()) {
                try {
                    $this->layoutService->loadLayout($layout->getId());
                    self::fail('Layout in draft status with existing published version loaded.');
                } catch (NotFoundException $e) {
                    // Do nothing
                }
            }
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::getLayoutsCount
     */
    public function testGetLayoutsCount(): void
    {
        self::assertSame(3, $this->layoutService->getLayoutsCount());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::getLayoutsCount
     */
    public function testGetLayoutsCountWithUnpublishedLayouts(): void
    {
        self::assertSame(5, $this->layoutService->getLayoutsCount(true));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadSharedLayouts
     */
    public function testLoadSharedLayouts(): void
    {
        $layouts = $this->layoutService->loadSharedLayouts();

        self::assertCount(2, $layouts);

        foreach ($layouts as $layout) {
            self::assertTrue($layout->isShared());
            self::assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::getSharedLayoutsCount
     */
    public function testGetSharedLayoutsCount(): void
    {
        self::assertSame(2, $this->layoutService->getSharedLayoutsCount());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadAllLayouts
     */
    public function testLoadAllLayouts(): void
    {
        $layouts = $this->layoutService->loadAllLayouts();

        self::assertCount(5, $layouts);

        foreach ($layouts as $layout) {
            self::assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::getAllLayoutsCount
     */
    public function testGetAllLayoutsCount(): void
    {
        self::assertSame(5, $this->layoutService->getAllLayoutsCount());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadRelatedLayouts
     */
    public function testLoadRelatedLayouts(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $layouts = $this->layoutService->loadRelatedLayouts($sharedLayout);

        self::assertCount(1, $layouts);

        foreach ($layouts as $layout) {
            self::assertFalse($layout->isShared());
            self::assertTrue($layout->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadRelatedLayouts
     */
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Related layouts can only be loaded for published shared layouts.');

        $sharedLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::loadRelatedLayouts
     */
    public function testLoadRelatedLayoutsThrowsBadStateExceptionWithNonSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Related layouts can only be loaded for shared layouts.');

        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $this->layoutService->loadRelatedLayouts($sharedLayout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCount(): void
    {
        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $count = $this->layoutService->getRelatedLayoutsCount($sharedLayout);

        self::assertSame(1, $count);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonPublishedSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Count of related layouts can only be loaded for published shared layouts.');

        $sharedLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCountThrowsBadStateExceptionWithNonSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Count of related layouts can only be loaded for shared layouts.');

        $sharedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $this->layoutService->getRelatedLayoutsCount($sharedLayout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::hasStatus
     */
    public function testHasStatus(): void
    {
        self::assertTrue($this->layoutService->hasStatus(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'), Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::hasStatus
     */
    public function testHasStatusReturnsFalse(): void
    {
        self::assertFalse($this->layoutService->hasStatus(Uuid::fromString('8626a1ca-6413-5f54-acef-de7db06272ce'), Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::layoutExists
     */
    public function testLayoutExists(): void
    {
        self::assertTrue($this->layoutService->layoutExists(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::layoutExists
     */
    public function testLayoutExistsReturnsFalse(): void
    {
        self::assertFalse($this->layoutService->layoutExists(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff')));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameExists(): void
    {
        self::assertTrue($this->layoutService->layoutNameExists('My layout'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedLayoutId(): void
    {
        self::assertFalse($this->layoutService->layoutNameExists('My layout', Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::layoutNameExists
     */
    public function testLayoutNameNotExists(): void
    {
        self::assertFalse($this->layoutService->layoutNameExists('Non existing'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::linkZone
     */
    public function testLinkZone(): void
    {
        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);

        $updatedZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');

        self::assertInstanceOf(Zone::class, $updatedZone->getLinkedZone());
        self::assertTrue($updatedZone->getLinkedZone()->isPublished());
        self::assertSame($linkedZone->getLayoutId()->toString(), $updatedZone->getLinkedZone()->getLayoutId()->toString());
        self::assertSame($linkedZone->getIdentifier(), $updatedZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWhenInSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Zone cannot be in the shared layout.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('399ad9ac-777a-50ba-945a-06e9f57add12'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Only draft zones can be linked.');

        $zone = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWithNonPublishedLinkedZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Zones can only be linked to published zones.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWhenLinkedZoneNotInSharedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::linkZone
     */
    public function testLinkZoneThrowsBadStateExceptionWhenInTheSameLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "linkedZone" has an invalid state. Linked zone needs to be in a different layout.');

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('left');
        $linkedZone = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        $this->layoutService->linkZone($zone, $linkedZone);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::unlinkZone
     */
    public function testUnlinkZone(): void
    {
        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        $this->layoutService->unlinkZone($zone);

        $updatedZone = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        self::assertNull($updatedZone->getLinkedZone());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::unlinkZone
     */
    public function testUnlinkZoneThrowsBadStateExceptionWithNonDraftZone(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "zone" has an invalid state. Only draft zones can be unlinked.');

        $zone = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'))->getZone('top');

        $this->layoutService->unlinkZone($zone);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout(): void
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My new layout',
            'en',
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        self::assertTrue($createdLayout->isDraft());

        self::assertGreaterThan(new DateTimeImmutable('@0'), $createdLayout->getCreated());

        self::assertSame(
            $createdLayout->getCreated()->format(DateTimeInterface::ATOM),
            $createdLayout->getModified()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayoutWithCustomUuid(): void
    {
        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            'My new layout',
            'en',
        );

        $layoutCreateStruct->uuid = Uuid::fromString('5f35d4d3-8fa7-4602-9d4c-c74c2b16e3d7');

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        self::assertTrue($createdLayout->isDraft());
        self::assertGreaterThan(new DateTimeImmutable('@0'), $createdLayout->getCreated());
        self::assertSame($layoutCreateStruct->uuid->toString(), $createdLayout->getId()->toString());

        self::assertSame(
            $createdLayout->getCreated()->format(DateTimeInterface::ATOM),
            $createdLayout->getModified()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createLayout
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createLayout
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::addTranslation
     */
    public function testAddTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $updatedLayout = $this->layoutService->addTranslation($layout, 'de', 'en');

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        self::assertSame(['en', 'hr', 'de'], $updatedLayout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en', 'hr', 'de']);
        foreach ($layoutBlocks as $layoutBlock) {
            $layoutBlock->isTranslatable() ?
                self::assertContains('de', $layoutBlock->getAvailableLocales()) :
                self::assertNotContains('de', $layoutBlock->getAvailableLocales());

            $layoutBlock->isTranslatable() ?
                self::assertContains('de', $layoutBlock->getAvailableLocales()) :
                self::assertNotContains('de', $layoutBlock->getAvailableLocales());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::addTranslation
     */
    public function testAddTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only add translation to draft layouts.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->addTranslation($layout, 'de', 'en');
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::addTranslation
     */
    public function testAddTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout already has the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->addTranslation($layout, 'en', 'hr');
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::setMainTranslation
     */
    public function testSetMainTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $updatedLayout = $this->layoutService->setMainTranslation($layout, 'hr');

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        self::assertSame('hr', $updatedLayout->getMainLocale());
        self::assertSame(['en', 'hr'], $updatedLayout->getAvailableLocales());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['hr', 'en']);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertSame('hr', $layoutBlock->getMainLocale());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::setMainTranslation
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only set main translation in draft layouts.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->setMainTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::setMainTranslation
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "mainLocale" has an invalid state. Layout does not have the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->setMainTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslation(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $updatedLayout = $this->layoutService->removeTranslation($layout, 'hr');
        self::assertNotContains('hr', $updatedLayout->getAvailableLocales());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

        $layoutBlocks = $this->blockService->loadLayoutBlocks($updatedLayout, ['en']);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertNotContains('hr', $layoutBlock->getAvailableLocales());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. You can only remove translations from draft layouts.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->removeTranslation($layout, 'hr');
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout does not have the provided locale.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->removeTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::removeTranslation
     */
    public function testRemoveTranslationThrowsBadStateExceptionWithMainLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Main translation cannot be removed from the layout.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $this->layoutService->removeTranslation($layout, 'en');
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayout(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';
        $layoutUpdateStruct->description = 'New description';

        $updatedLayout = $this->layoutService->updateLayout($layout, $layoutUpdateStruct);

        self::assertTrue($updatedLayout->isDraft());
        self::assertSame('New name', $updatedLayout->getName());
        self::assertSame('New description', $updatedLayout->getDescription());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::updateLayout
     */
    public function testUpdateLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only draft layouts can be updated.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';

        $this->layoutService->updateLayout($layout, $layoutUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::updateLayout
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayout(): void
    {
        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'New name';
        $copyStruct->description = 'New description';

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        self::assertSame($layout->isPublished(), $copiedLayout->isPublished());

        self::assertGreaterThan($layout->getCreated(), $copiedLayout->getCreated());

        self::assertSame(
            $copiedLayout->getCreated()->format(DateTimeInterface::ATOM),
            $copiedLayout->getModified()->format(DateTimeInterface::ATOM),
        );

        self::assertNotSame($layout->getId()->toString(), $copiedLayout->getId()->toString());
        self::assertSame('New name', $copiedLayout->getName());
        self::assertSame('New description', $copiedLayout->getDescription());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayoutThrowsBadStateExceptionOnExistingLayoutName(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.');

        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'My other layout';

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->copyLayout($layout, $copyStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::changeLayoutType
     */
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

        self::assertSame($layout->getId()->toString(), $updatedLayout->getId()->toString());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        self::assertSame('b07d3a85-bcdb-5af2-9b6f-deba36c700e7', $topZoneBlocks[0]->getId()->toString());
        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $topZoneBlocks[1]->getId()->toString());
        self::assertSame('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e', $topZoneBlocks[2]->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::changeLayoutType
     */
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

        self::assertSame($layout->getId()->toString(), $updatedLayout->getId()->toString());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        self::assertSame('b07d3a85-bcdb-5af2-9b6f-deba36c700e7', $topZoneBlocks[0]->getId()->toString());
        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $topZoneBlocks[1]->getId()->toString());
        self::assertSame('c2a30ea3-95ef-55b0-a584-fbcfd93cec9e', $topZoneBlocks[2]->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::changeLayoutType
     */
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

        self::assertSame($layout->getId()->toString(), $updatedLayout->getId()->toString());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        self::assertTrue($topZone->hasLinkedZone());

        $newTopZone = $layout->getZone('top');

        self::assertInstanceOf(Zone::class, $topZone->getLinkedZone());
        self::assertInstanceOf(Zone::class, $newTopZone->getLinkedZone());

        self::assertSame($newTopZone->getLinkedZone()->getLayoutId()->toString(), $topZone->getLinkedZone()->getLayoutId()->toString());
        self::assertSame($newTopZone->getLinkedZone()->getIdentifier(), $topZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::changeLayoutType
     */
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

        self::assertSame($layout->getId()->toString(), $updatedLayout->getId()->toString());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        self::assertTrue($topZone->hasLinkedZone());

        $newTopZone = $layout->getZone('top');

        self::assertInstanceOf(Zone::class, $topZone->getLinkedZone());
        self::assertInstanceOf(Zone::class, $newTopZone->getLinkedZone());

        self::assertSame($newTopZone->getLinkedZone()->getLayoutId()->toString(), $topZone->getLinkedZone()->getLayoutId()->toString());
        self::assertSame($newTopZone->getLinkedZone()->getIdentifier(), $topZone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::changeLayoutType
     */
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

        self::assertSame($layout->getId()->toString(), $updatedLayout->getId()->toString());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_a', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        self::assertFalse($topZone->hasLinkedZone());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::changeLayoutType
     */
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

        self::assertSame($layout->getId()->toString(), $updatedLayout->getId()->toString());
        self::assertSame($layout->getStatus(), $updatedLayout->getStatus());
        self::assertSame('4_zones_b', $updatedLayout->getLayoutType()->getIdentifier());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $updatedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $updatedLayout->getModified());

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

        self::assertFalse($topZone->hasLinkedZone());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::changeLayoutType
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraft(): void
    {
        $layout = $this->layoutService->loadLayout(Uuid::fromString('7900306c-0351-5f0a-9b33-5d4f5a1f3943'));
        $draftLayout = $this->layoutService->createDraft($layout);

        self::assertTrue($draftLayout->isDraft());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $draftLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftWithDiscardingExistingDraft(): void
    {
        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $draftLayout = $this->layoutService->createDraft($layout, true);

        self::assertTrue($draftLayout->isDraft());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $draftLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $draftLayout->getModified());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Drafts can only be created from published layouts.');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9'));
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::createDraft
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. The provided layout already has a draft.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->createDraft($layout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::discardDraft
     */
    public function testDiscardDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"');

        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->discardDraft($layout);

        $this->layoutService->loadLayoutDraft($layout->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::discardDraft
     */
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only drafts can be discarded.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->discardDraft($layout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayout(): void
    {
        $layout = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $currentlyPublishedLayout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $publishedLayout = $this->layoutService->publishLayout($layout);

        self::assertTrue($publishedLayout->isPublished());

        self::assertSame(
            $layout->getCreated()->format(DateTimeInterface::ATOM),
            $publishedLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($layout->getModified(), $publishedLayout->getModified());

        $archivedLayout = $this->layoutService->loadLayoutArchive(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));

        self::assertSame(
            $currentlyPublishedLayout->getModified()->format(DateTimeInterface::ATOM),
            $archivedLayout->getModified()->format(DateTimeInterface::ATOM),
        );

        try {
            $this->layoutService->loadLayoutDraft($layout->getId());
            self::fail('Draft layout still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::publishLayout
     */
    public function testPublishLayoutThrowsBadStateExceptionWithNonDraftLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only drafts can be published.');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->publishLayout($layout);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchive(): void
    {
        $originalLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $publishedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $restoredLayout = $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );

        self::assertTrue($restoredLayout->isDraft());
        self::assertSame($publishedLayout->getName(), $restoredLayout->getName());

        self::assertSame(
            $originalLayout->getCreated()->format(DateTimeInterface::ATOM),
            $restoredLayout->getCreated()->format(DateTimeInterface::ATOM),
        );

        self::assertGreaterThan($originalLayout->getModified(), $restoredLayout->getModified());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchiveWithoutDraft(): void
    {
        $originalLayout = $this->layoutService->loadLayoutDraft(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $this->layoutService->discardDraft($originalLayout);

        $publishedLayout = $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac'));
        $restoredLayout = $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );

        self::assertTrue($restoredLayout->isDraft());
        self::assertSame($publishedLayout->getName(), $restoredLayout->getName());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Only archived layouts can be restored.');

        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::restoreFromArchive
     */
    public function testRestoreFromArchiveThrowsNotFoundExceptionOnNonExistingPublishedVersion(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "71cbe281-430c-51d5-8e21-c3cc4e656dac"');

        $this->layoutHandler->deleteLayout(2, Layout::STATUS_PUBLISHED);
        $this->layoutService->restoreFromArchive(
            $this->layoutService->loadLayoutArchive(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::deleteLayout
     */
    public function testDeleteLayout(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "81168ed3-86f9-55ea-b153-101f96f2c136"');

        $layout = $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'));
        $this->layoutService->deleteLayout($layout);

        $this->layoutService->loadLayout($layout->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::newLayoutCreateStruct
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::newLayoutUpdateStruct
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::newLayoutUpdateStruct
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::newLayoutCopyStruct
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutService::newLayoutCopyStruct
     */
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
