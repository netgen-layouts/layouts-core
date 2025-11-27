<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Doctrine\Common\Collections\ArrayCollection;

use function array_filter;
use function array_map;
use function array_values;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<string, \Netgen\Layouts\Parameters\Parameter>
 */
final class ParameterList extends ArrayCollection
{
    /**
     * @param array<string, \Netgen\Layouts\Parameters\Parameter> $parameters
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct(
            array_filter(
                $parameters,
                static fn (Parameter $parameter): bool => true,
            ),
        );
    }

    /**
     * @return array<string, \Netgen\Layouts\Parameters\Parameter>
     */
    public function getParameters(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    public function getParameterNames(): array
    {
        return array_values(
            array_map(
                static fn (Parameter $parameter): string => $parameter->name,
                $this->getParameters(),
            ),
        );
    }
}
