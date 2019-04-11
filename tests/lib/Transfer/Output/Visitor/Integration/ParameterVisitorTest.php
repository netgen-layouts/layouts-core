<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterTypeWithExportImport;
use Netgen\Layouts\Transfer\Output\Visitor\ParameterVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

abstract class ParameterVisitorTest extends VisitorTest
{
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
            [Parameter::fromArray(['parameterDefinition' => ParameterDefinition::fromArray(['type' => new ParameterTypeWithExportImport()])]), 'parameter/parameter.json'],
        ];
    }
}
