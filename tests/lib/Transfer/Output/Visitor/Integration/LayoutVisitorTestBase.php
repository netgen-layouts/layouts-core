<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Transfer\Output\Visitor\LayoutVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Layout\Layout>
 */
abstract class LayoutVisitorTestBase extends VisitorTestBase
{
    final public function getVisitor(): VisitorInterface
    {
        return new LayoutVisitor();
    }

    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Layout(), true],
            [new Zone(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['layout/layout_1.json', '81168ed3-86f9-55ea-b153-101f96f2c136'],
            ['layout/layout_2.json', '71cbe281-430c-51d5-8e21-c3cc4e656dac'],
            ['layout/layout_5.json', '399ad9ac-777a-50ba-945a-06e9f57add12'],
            ['layout/layout_7.json', '4b0202b3-5d06-5962-ae0c-bbeb25ee3503'],
        ];
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Layout
    {
        return $this->layoutService->loadLayout(Uuid::fromString($id));
    }
}
