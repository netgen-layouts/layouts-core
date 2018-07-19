<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterTypeWithExportImport;
use Netgen\BlockManager\Transfer\Output\Visitor\ParameterVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class ParameterVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
    }

    public function getVisitor(): VisitorInterface
    {
        return new ParameterVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Parameter(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [new Parameter(['parameterDefinition' => new ParameterDefinition(['type' => new ParameterTypeWithExportImport()])]), 'parameter/parameter.json'],
        ];
    }
}
