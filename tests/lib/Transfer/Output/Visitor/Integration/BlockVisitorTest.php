<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\BlockVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTest<\Netgen\Layouts\API\Values\Block\Block>
 */
abstract class BlockVisitorTest extends VisitorTest
{
    public function getVisitor(): VisitorInterface
    {
        return new BlockVisitor($this->blockService);
    }

    public function acceptDataProvider(): array
    {
        return [
            [new Block(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitDataProvider(): array
    {
        return [
            [function (): Block { return $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de')); }, 'block/block_31.json'],
            [function (): Block { return $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59')); }, 'block/block_33.json'],
        ];
    }
}
