<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\API\Values\ParameterStructTrait;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;

final class QueryCreateStruct implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * Query type for which the new query will be created.
     */
    private QueryTypeInterface $queryType;

    public function __construct(QueryTypeInterface $queryType)
    {
        $this->queryType = $queryType;
        $this->fillDefault($this->queryType);
    }

    /**
     * Returns the query  type that will be used to create a query with this struct.
     */
    public function getQueryType(): QueryTypeInterface
    {
        return $this->queryType;
    }

    /**
     * Fills the parameter values based on provided array of values.
     *
     * If any of the parameters is missing from the input array, the default value
     * based on parameter definition from the query type will be used.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     *
     * @param array<string, mixed> $values
     */
    public function fillParametersFromHash(array $values, bool $doImport = false): void
    {
        $this->fillFromHash($this->queryType, $values, $doImport);
    }
}
