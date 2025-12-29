<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\ConfigVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Config\Config>
 */
abstract class ConfigVisitorTestBase extends VisitorTestBase
{
    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Config(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['config/block_31.json', '28df256a-2467-5527-b398-9269ccc652de', 'key'],
        ];
    }

    final protected function getVisitor(): VisitorInterface
    {
        return new ConfigVisitor();
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Config
    {
        return $this->blockService->loadBlock(Uuid::fromString($id))->getConfig($additionalParameters[0]);
    }
}
