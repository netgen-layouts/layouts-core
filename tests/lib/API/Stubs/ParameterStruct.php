<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Stubs;

use Netgen\Layouts\API\Values\ParameterStruct as APIParameterStruct;
use Netgen\Layouts\API\Values\ParameterStructTrait;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;

final class ParameterStruct implements APIParameterStruct
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

    /**
     * @param array<string, mixed> $values
     */
    public function fillParametersFromHash(ParameterDefinitionCollectionInterface $definitions, array $values, bool $doImport = false): void
    {
        $this->fillFromHash($definitions, $values, $doImport);
    }
}
