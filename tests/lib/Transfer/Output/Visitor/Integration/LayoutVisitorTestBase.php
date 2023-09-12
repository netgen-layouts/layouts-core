<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Transfer\Output\Visitor\LayoutVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Layout\Layout>
 */
abstract class LayoutVisitorTestBase extends VisitorTestBase
{
    public function getVisitor(): VisitorInterface
    {
        return new LayoutVisitor();
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new Layout(), true],
            [new Zone(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [fn (): Layout => $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')), 'layout/layout_1.json'],
            [fn (): Layout => $this->layoutService->loadLayout(Uuid::fromString('71cbe281-430c-51d5-8e21-c3cc4e656dac')), 'layout/layout_2.json'],
            [fn (): Layout => $this->layoutService->loadLayout(Uuid::fromString('399ad9ac-777a-50ba-945a-06e9f57add12')), 'layout/layout_5.json'],
            [fn (): Layout => $this->layoutService->loadLayoutDraft(Uuid::fromString('4b0202b3-5d06-5962-ae0c-bbeb25ee3503')), 'layout/layout_7.json'],
        ];
    }
}
