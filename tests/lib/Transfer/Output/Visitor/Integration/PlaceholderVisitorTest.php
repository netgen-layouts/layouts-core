<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\Visitor\PlaceholderVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

abstract class PlaceholderVisitorTest extends VisitorTest
{
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Implementation requires sub-visitor');

        $this->getVisitor()->visit(new Placeholder());
    }

    public function getVisitor(): VisitorInterface
    {
        return new PlaceholderVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Placeholder(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Placeholder { return $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'))->getPlaceholder('left'); }, 'placeholder/block_33_left.json'],
            [function (): Placeholder { return $this->blockService->loadBlock(Uuid::fromString('e666109d-f1db-5fd5-97fa-346f50e9ae59'))->getPlaceholder('right'); }, 'placeholder/block_33_right.json'],
        ];
    }
}
