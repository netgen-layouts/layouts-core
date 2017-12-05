<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\ValueObject;

final class QueryCreateStruct extends ValueObject implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * Query type for which the new query will be created.
     *
     * Required.
     *
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public $queryType;

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     * @param array $values
     */
    public function fillParameters(QueryTypeInterface $queryType, array $values = array())
    {
        $this->fill($queryType, $values);
    }

    /**
     * Fills the parameter values based on provided query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function fillParametersFromQuery(Query $query)
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
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     * @param array $values
     * @param bool $doImport
     */
    public function fillParametersFromHash(QueryTypeInterface $queryType, array $values = array(), $doImport = false)
    {
        $this->fillFromHash($queryType, $values, $doImport);
    }
}
