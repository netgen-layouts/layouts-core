<?php

namespace Netgen\BlockManager\Layout\Form\DataTransformer;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LayoutTypeTransformer implements DataTransformerInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     */
    public function __construct(LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
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

        if (!$value instanceof LayoutType) {
            throw new TransformationFailedException('Provided value is not a layout type.');
        }

        return $value->getIdentifier();
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
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
            return null;
        }

        if (!$this->layoutTypeRegistry->hasLayoutType($value)) {
            throw new TransformationFailedException('Provided value is not a layout type.');
        }

        return $this->layoutTypeRegistry->getLayoutType($value);
    }
}
