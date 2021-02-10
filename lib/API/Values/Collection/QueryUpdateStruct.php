<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\API\Values\ParameterStructTrait;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;

final class QueryUpdateStruct implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * The locale which will be updated.
     *
     * Required.
     */
    public string $locale;

    /**
     * Fills the parameter values based on provided query.
     */
    public function fillParametersFromQuery(Query $query): void
    {
        $this->fillFromCollection($query->getQueryType(), $query);
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
    public function fillParametersFromHash(QueryTypeInterface $queryType, array $values, bool $doImport = false): void
    {
        $this->fillFromHash($queryType, $values, $doImport);
    }
}
