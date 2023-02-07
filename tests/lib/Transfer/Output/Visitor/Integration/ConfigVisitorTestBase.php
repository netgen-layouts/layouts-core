<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\ConfigVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Config\Config>
 */
abstract class ConfigVisitorTestBase extends VisitorTestBase
{
    public function getVisitor(): VisitorInterface
    {
        return new ConfigVisitor();
    }

    public static function acceptDataProvider(): array
    {
        return [
            [new Config(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): array
    {
        return [
            [fn (): Config => $this->blockService->loadBlock(Uuid::fromString('28df256a-2467-5527-b398-9269ccc652de'))->getConfig('key'), 'config/block_31.json'],
        ];
    }
}
