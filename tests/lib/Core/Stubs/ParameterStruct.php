<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\ParameterStruct as APIParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\BlockManager\Value as BaseValue;

final class ParameterStruct extends BaseValue implements APIParameterStruct
{
    use ParameterStructTrait;

    public function fillDefaultParameters(ParameterDefinitionCollectionInterface $definitions): void
    {
        $this->fillDefault($definitions);
    }

    public function fillParametersFromCollection(ParameterDefinitionCollectionInterface $definitions, ParameterCollectionInterface $parameters): void
    {
        $this->fillFromCollection($definitions, $parameters);
    }

    public function fillParametersFromHash(ParameterDefinitionCollectionInterface $definitions, array $values, bool $doImport = false): void
    {
        $this->fillFromHash($definitions, $values, $doImport);
    }
}
