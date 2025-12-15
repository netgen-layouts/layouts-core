<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\BlockVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Block\Block>
 */
abstract class BlockVisitorTestBase extends VisitorTestBase
{
    final public function getVisitor(): VisitorInterface
    {
        return new BlockVisitor($this->blockService);
    }

    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Block(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['block/block_31.json', '28df256a-2467-5527-b398-9269ccc652de'],
            ['block/block_33.json', 'e666109d-f1db-5fd5-97fa-346f50e9ae59'],
        ];
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Block
    {
        return $this->blockService->loadBlock(Uuid::fromString($id));
    }
}
