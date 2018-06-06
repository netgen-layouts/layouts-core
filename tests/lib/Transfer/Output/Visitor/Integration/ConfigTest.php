<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Config\Config as ConfigValue;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\Config;

abstract class ConfigTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor()
    {
        $this->getVisitor()->visit(new ConfigValue());
    }

    public function getVisitor()
    {
        return new Config();
    }

    public function acceptProvider()
    {
        return [
            [new ConfigValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->blockService->loadBlock(31)->getConfig('http_cache'); }, 'config/block_31.json'],
        ];
    }
}
