<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\PlaceholderVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Block\Placeholder>
 */
abstract class PlaceholderVisitorTestBase extends VisitorTestBase
{
    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Placeholder(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['placeholder/block_33_left.json', 'e666109d-f1db-5fd5-97fa-346f50e9ae59', 'left'],
            ['placeholder/block_33_right.json', 'e666109d-f1db-5fd5-97fa-346f50e9ae59', 'right'],
        ];
    }

    final protected function getVisitor(): VisitorInterface
    {
        return new PlaceholderVisitor();
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Placeholder
    {
        return $this->blockService->loadBlock(Uuid::fromString($id))->getPlaceholder($additionalParameters[0]);
    }
}
