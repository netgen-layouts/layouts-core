<?php

namespace Netgen\BlockManager\Exception\View;

use Exception;
use Netgen\BlockManager\Exception\RuntimeException;

class ViewProviderException extends RuntimeException
{
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        if (empty($message)) {
            $message = 'An error occurred while building the view.';
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string $class
     *
     * @return \Netgen\BlockManager\Exception\View\ViewProviderException
     */
    public static function noViewProvider($class)
    {
        return new self(
            sprintf(
                'No view providers found for "%s" value object.',
                $class
            )
        );
    }

    /**
     * @param string $viewType
     * @param string $parameterName
     *
     * @return \Netgen\BlockManager\Exception\View\ViewProviderException
     */
    public static function noParameter($viewType, $parameterName)
    {
        return new self(
            sprintf(
                'To build the %s view, "%s" parameter needs to be provided.',
                $viewType,
                $parameterName
            )
        );
    }

    /**
     * @param string $viewType
     * @param string $parameterName
     * @param string $expectedType
     *
     * @return \Netgen\BlockManager\Exception\View\ViewProviderException
     */
    public static function invalidParameter($viewType, $parameterName, $expectedType)
    {
        return new self(
            sprintf(
                'To build the %s view, "%s" parameter needs to be of "%s" type.',
                $viewType,
                $parameterName,
                $expectedType
            )
        );
    }
}
