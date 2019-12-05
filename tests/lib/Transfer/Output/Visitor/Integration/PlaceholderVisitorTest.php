<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\PlaceholderVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTest<\Netgen\Layouts\API\Values\Block\Placeholder>
 */
abstract class PlaceholderVisitorTest extends VisitorTest
{
    public function getVisitor(): VisitorInterface
    {
        return new PlaceholderVisitor();
    }

    public function acceptDataProvider(): array
    {
        return [
            [new Placeholder(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitDataProvider(): array
    {
        return [
            [function (): Placeholder { return $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'))->getPlaceholder('left'); }, 'placeholder/block_33_left.json'],
            [function (): Placeholder { return $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'))->getPlaceholder('right'); }, 'placeholder/block_33_right.json'],
        ];
    }
}
