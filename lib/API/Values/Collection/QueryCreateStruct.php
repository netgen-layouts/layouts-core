<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
use Netgen\BlockManager\Value;

final class QueryCreateStruct extends Value implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * Query type for which the new query will be created.
     *
     * Required.
     *
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    public $queryType;

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     */
    public function fillParameters(QueryTypeInterface $queryType, array $values = []): void
    {
        $this->fill($queryType, $values);
    }

    /**
     * Fills the parameter values based on provided query.
     */
    public function fillParametersFromQuery(Query $query): void
    {
        $this->fillFromValue($query->getQueryType(), $query);
    }

    /**
     * Fills the parameter values based on provided array of values.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     */
    public function fillParametersFromHash(QueryTypeInterface $queryType, array $values = [], bool $doImport = false): void
    {
        $this->fillFromHash($queryType, $values, $doImport);
    }
}
