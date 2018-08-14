<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\ConfigVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class ConfigVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->getVisitor()->visit(new Config());
    }

    public function getVisitor(): VisitorInterface
    {
        return new ConfigVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Config(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Config { return $this->blockService->loadBlock(31)->getConfig('key'); }, 'config/block_31.json'],
        ];
    }
}
