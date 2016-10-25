<?php

namespace Netgen\BlockManager\Parameters\Form\DataTransformer;

use Netgen\BlockManager\Parameters\Parameter\Uri;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UriDataTransformer implements DataTransformerInterface
{
    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return mixed The value in the transformed representation
     */
    public function transform($value)
    {
        if (empty($value) || !is_array($value)) {
            return array();
        }

        if (!isset($value['link_type'])) {
            return array();
        }

        $transformedValue = $value;

        if (in_array($value['link_type'], array(Uri::LINK_TYPE_URL, Uri::LINK_TYPE_EMAIL, Uri::LINK_TYPE_INTERNAL))) {
            $transformedValue[$value['link_type']] = $value['link'];
        }

        return $transformedValue;
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
     * @throws TransformationFailedException When the transformation fails
     *
     * @return mixed The value in the original representation
     */
    public function reverseTransform($value)
    {
        if (empty($value) || !is_array($value)) {
            return array();
        }

        if (!isset($value['link_type'])) {
            return array();
        }

        $transformedValue = array(
            'link_type' => $value['link_type'],
            'link' => null,
            'link_suffix' => isset($value['link_suffix']) ? $value['link_suffix'] : '',
            'new_window' => isset($value['new_window']) && $value['new_window'] ? true : false,
        );

        if (in_array($value['link_type'], array(Uri::LINK_TYPE_URL, Uri::LINK_TYPE_EMAIL, Uri::LINK_TYPE_INTERNAL))) {
            $transformedValue['link'] = isset($value[$value['link_type']]) ?
                $value[$value['link_type']] :
                null;
        }

        return $transformedValue;
    }
}
