<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\View;

use Exception as BaseException;
use Netgen\BlockManager\Exception\Exception;
use RuntimeException;

final class TemplateResolverException extends RuntimeException implements Exception
{
    public function __construct($message = '', $code = 0, BaseException $previous = null)
    {
        if (empty($message)) {
            $message = 'An error occurred while resolving the view template.';
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\View\TemplateResolverException
     */
    public static function noTemplateMatcher($identifier)
    {
        return new self(
            sprintf(
                'No template matcher could be found with identifier "%s".',
                $identifier
            )
        );
    }

    /**
     * @param string $viewType
     * @param string $context
     *
     * @return \Netgen\BlockManager\Exception\View\TemplateResolverException
     */
    public static function noTemplateMatch($viewType, $context)
    {
        return new self(
            sprintf(
                'No template match could be found for "%s" view and context "%s".',
                $viewType,
                $context
            )
        );
    }
}
