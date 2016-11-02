<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Netgen\BlockManager\API\Values\Value;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Netgen\BlockManager\Exception\InvalidArgumentException;

abstract class ParamConverter implements ParamConverterInterface
{
    const ROUTE_STATUS_PARAM = '_ngbm_status';

    /**
     * Stores the object in the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request The request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverterConfiguration $configuration)
    {
        $sourceAttributeNames = $this->getSourceAttributeNames();
        foreach ($sourceAttributeNames as $sourceAttributeName) {
            if (!$request->attributes->has($sourceAttributeName)) {
                return false;
            }
        }

        $values = array();
        foreach ($sourceAttributeNames as $sourceAttributeName) {
            $values[$sourceAttributeName] = $request->attributes->get($sourceAttributeName);

            if (empty($values[$sourceAttributeName])) {
                if ($configuration->isOptional()) {
                    return false;
                }

                throw new InvalidArgumentException(
                    $sourceAttributeName,
                    'Required request attribute is empty.'
                );
            }
        }

        $routeStatusParam = $request->attributes->get(self::ROUTE_STATUS_PARAM);
        $queryPublishedParam = $request->query->get('published');

        $values['published'] = false;
        if (in_array($routeStatusParam, array('published', 'draft'))) {
            $values['published'] = $routeStatusParam === 'published';
        } elseif ($queryPublishedParam === 'true') {
            $values['published'] = true;
        }

        $request->attributes->set(
            $this->getDestinationAttributeName(),
            $this->loadValueObject($values)
        );

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverterConfiguration $configuration)
    {
        return is_a($configuration->getClass(), $this->getSupportedClass(), true);
    }

    /**
     * Returns source attribute name.
     *
     * @return array
     */
    abstract public function getSourceAttributeNames();

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    abstract public function getDestinationAttributeName();

    /**
     * Returns the supported class.
     *
     * @return string
     */
    abstract public function getSupportedClass();

    /**
     * Returns the value object.
     *
     * @param array $values
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    abstract public function loadValueObject(array $values);
}
