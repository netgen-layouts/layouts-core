<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\ParameterException;
use PHPUnit\Framework\TestCase;

final class ParameterExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\ParameterException::noParameter
     */
    public function testNoParameter(): void
    {
        $exception = ParameterException::noParameter('param');

        $this->assertEquals(
            'Parameter with "param" name does not exist.',
            $exception->getMessage()
        );
    }
}
