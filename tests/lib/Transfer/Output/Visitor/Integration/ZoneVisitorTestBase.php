<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Transfer\Output\Visitor\ZoneVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Layout\Zone>
 */
abstract class ZoneVisitorTestBase extends VisitorTestBase
{
    final public function getVisitor(): VisitorInterface
    {
        return new ZoneVisitor($this->blockService);
    }

    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Zone(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['zone/zone_2_top.json', '71cbe281-430c-51d5-8e21-c3cc4e656dac', 'top'],
            ['zone/zone_2_right.json', '71cbe281-430c-51d5-8e21-c3cc4e656dac', 'right'],
            ['zone/zone_6_bottom.json', '7900306c-0351-5f0a-9b33-5d4f5a1f3943', 'bottom'],
        ];
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Zone
    {
        return $this->layoutService->loadLayout(Uuid::fromString($id))->getZone($additionalParameters[0]);
    }
}
