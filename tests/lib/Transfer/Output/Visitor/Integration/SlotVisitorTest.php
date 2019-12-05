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
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTest<\Netgen\Layouts\API\Values\Collection\Slot>
 */
abstract class SlotVisitorTest extends VisitorTest
{
    public function getVisitor(): VisitorInterface
    {
        return new SlotVisitor();
    }

    public function acceptDataProvider(): array
    {
        return [
            [new Slot(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitDataProvider(): array
    {
        return [
            [function (): Slot { return $this->collectionService->loadSlot(Uuid::fromString('c63c9523-e579-4dc9-b1d2-f9d12470a014')); }, 'slot/slot_3.json'],
            [function (): Slot { return $this->collectionService->loadSlot(Uuid::fromString('2e0bbc16-8f14-4740-aa58-fbf6d547e734')); }, 'slot/slot_4.json'],
        ];
    }
}
