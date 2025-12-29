<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\SlotVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Collection\Slot>
 */
abstract class SlotVisitorTestBase extends VisitorTestBase
{
    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Slot(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['slot/slot_3.json', 'c63c9523-e579-4dc9-b1d2-f9d12470a014'],
            ['slot/slot_4.json', '2e0bbc16-8f14-4740-aa58-fbf6d547e734'],
        ];
    }

    final protected function getVisitor(): VisitorInterface
    {
        return new SlotVisitor();
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Slot
    {
        return $this->collectionService->loadSlot(Uuid::fromString($id));
    }
}
