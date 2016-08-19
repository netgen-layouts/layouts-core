<?php

namespace Netgen\BlockManager\Parameters\FormMapper\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ParameterFilterDataTransformer implements DataTransformerInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterFilterInterface[]
     */
    protected $parameterFilters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterFilterInterface[] $parameterFilters
     */
    public function __construct(array $parameterFilters = array())
    {
        $this->parameterFilters = $parameterFilters;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException When the transformation fails
     *
     * @return mixed The value in the transformed representation
     */
    public function transform($value)
    {
        if ($value === null) {
            return '';
        }

        return $value;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException When the transformation fails
     *
     * @return mixed The value in the original representation
     */
    public function reverseTransform($value)
    {
        if ($value === '') {
            return;
        }

        foreach ($this->parameterFilters as $parameterFilter) {
            $value = $parameterFilter->filter($value);
        }

        return $value;
    }
}
