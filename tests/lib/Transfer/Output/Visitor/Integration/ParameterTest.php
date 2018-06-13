<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Parameters\Parameter as ParameterValue;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterTypeWithExportImport;
use Netgen\BlockManager\Transfer\Output\Visitor\Parameter;

abstract class ParameterTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();
    }

    public function getVisitor()
    {
        return new Parameter();
    }

    public function acceptProvider()
    {
        return [
            [new ParameterValue(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [new ParameterValue(['parameterDefinition' => new ParameterDefinition(['type' => new ParameterTypeWithExportImport()])]), 'parameter/parameter.json'],
        ];
    }
}
