<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

abstract class ParamConverter implements ParamConverterInterface
{
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
        $sourceAttributeName = $this->getSourceAttributeName();
        if (!$request->attributes->has($sourceAttributeName)) {
            return false;
        }

        $valueId = $request->attributes->get($sourceAttributeName);
        if (empty($valueId)) {
            if ($configuration->isOptional()) {
                return false;
            }

            throw new UnexpectedValueException(
                sprintf(
                    'Required request attribute "%s" is empty',
                    $sourceAttributeName
                )
            );
        }

        $request->attributes->set(
            $this->getDestinationAttributeName(),
            $this->loadValueObject($valueId)
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
     * @return string
     */
    abstract public function getSourceAttributeName();

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
     * @param int|string $valueId
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    abstract public function loadValueObject($valueId);
}
