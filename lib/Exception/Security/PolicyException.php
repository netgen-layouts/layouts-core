<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Security;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class PolicyException extends InvalidArgumentException implements Exception
{
    public static function policyNotSupported(string $policy): self
    {
        return new self(
            sprintf(
                'Policy "%s" is not supported.',
                $policy
            )
        );
    }
}
