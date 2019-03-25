<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Security;

use Netgen\BlockManager\Exception\Security\PolicyException;
use PHPUnit\Framework\TestCase;

final class PolicyExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Security\PolicyException::policyNotSupported
     */
    public function testPolicyNotSupported(): void
    {
        $exception = PolicyException::policyNotSupported('test');

        self::assertSame(
            'Policy "test" is not supported.',
            $exception->getMessage()
        );
    }
}
