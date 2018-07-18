<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;

final class QueryCreateStruct implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * Query type for which the new query will be created.
     *
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    private $queryType;

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
     */
    public function fillParametersFromHash(array $values, bool $doImport = false): void
    {
        $this->fillFromHash($this->queryType, $values, $doImport);
    }
}
