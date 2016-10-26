<?php

namespace Netgen\BlockManager\Parameters\Form\DataTransformer;

use Netgen\BlockManager\Parameters\Parameter\Link;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LinkDataTransformer implements DataTransformerInterface
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
            return null;
        }

        if (!isset($value['link_type']) || !isset($value['link'])) {
            return null;
        }

        if (!in_array($value['link_type'], array(Link::LINK_TYPE_URL, Link::LINK_TYPE_EMAIL, Link::LINK_TYPE_INTERNAL))) {
            return null;
        }

        $transformedValue = $value;

        if ($value['link_type'] === Link::LINK_TYPE_URL) {
            $transformedValue['url'] = $value['link'];
        } elseif ($value['link_type'] === Link::LINK_TYPE_EMAIL) {
            $transformedValue['email'] = $value['link'];
        } elseif ($value['link_type'] === Link::LINK_TYPE_INTERNAL) {
            $link = parse_url($value['link']);
            if (!empty($link['scheme']) && !empty($link['host'])) {
                $transformedValue['internal'] = array(
                    'item_id' => $link['host'],
                    'item_type' => $link['scheme'],
                );
            }
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
            return null;
        }

        if (!isset($value['link_type'])) {
            return null;
        }

        if (!in_array($value['link_type'], array(Link::LINK_TYPE_URL, Link::LINK_TYPE_EMAIL, Link::LINK_TYPE_INTERNAL))) {
            return null;
        }

        $transformedValue = array(
            'link_type' => $value['link_type'],
            'link' => null,
            'link_suffix' => isset($value['link_suffix']) ? $value['link_suffix'] : '',
            'new_window' => isset($value['new_window']) && $value['new_window'] ? true : false,
        );

        if ($value['link_type'] === Link::LINK_TYPE_URL && isset($value['url'])) {
            $transformedValue['link'] = $value['url'];
        } elseif ($value['link_type'] === Link::LINK_TYPE_EMAIL && isset($value['email'])) {
            $transformedValue['link'] = $value['email'];
        } elseif ($value['link_type'] === Link::LINK_TYPE_INTERNAL) {
            if (!empty($value['internal']['item_id']) && !empty($value['internal']['item_type'])) {
                $transformedValue['link'] = $value['internal']['item_type'] . '://' . $value['internal']['item_id'];
            }
        }

        return $transformedValue;
    }
}
