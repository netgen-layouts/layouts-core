<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\View;

use Netgen\Layouts\Exception\Exception;
use RuntimeException;
use Throwable;

use function sprintf;

final class ViewProviderException extends RuntimeException implements Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if ($message === '') {
            $message = 'An error occurred while building the view.';
        }

        parent::__construct($message, $code, $previous);
    }

    public static function noViewProvider(string $class): self
    {
        return new self(
            sprintf(
                'No view providers found for "%s" value.',
                $class,
            ),
        );
    }

    public static function noParameter(string $viewType, string $parameterName): self
    {
        return new self(
            sprintf(
                'To build the %s view, "%s" parameter needs to be provided.',
                $viewType,
                $parameterName,
            ),
        );
    }

    public static function invalidParameter(string $viewType, string $parameterName, string $expectedType): self
    {
        return new self(
            sprintf(
                'To build the %s view, "%s" parameter needs to be of "%s" type.',
                $viewType,
                $parameterName,
                $expectedType,
            ),
        );
    }
}
