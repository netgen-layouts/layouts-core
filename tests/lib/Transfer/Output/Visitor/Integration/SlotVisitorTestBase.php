<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\SlotVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Collection\Slot>
 */
abstract class SlotVisitorTestBase extends VisitorTestBase
{
    public function getVisitor(): VisitorInterface
    {
        return new SlotVisitor();
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new Slot(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [fn (): Slot => $this->collectionService->loadSlot(Uuid::fromString('c63c9523-e579-4dc9-b1d2-f9d12470a014')), 'slot/slot_3.json'],
            [fn (): Slot => $this->collectionService->loadSlot(Uuid::fromString('2e0bbc16-8f14-4740-aa58-fbf6d547e734')), 'slot/slot_4.json'],
        ];
    }
}
