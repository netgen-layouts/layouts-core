<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\View;

use Netgen\BlockManager\Exception\Exception;
use RuntimeException;
use Throwable;

final class TemplateResolverException extends RuntimeException implements Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if ($message === '') {
            $message = 'An error occurred while resolving the view template.';
        }

        parent::__construct($message, $code, $previous);
    }

    public static function noTemplateMatcher(string $identifier): self
    {
        return new self(
            sprintf(
                'No template matcher could be found with identifier "%s".',
                $identifier
            )
        );
    }

    public static function noTemplateMatch(string $viewType, string $context): self
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
