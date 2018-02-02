<?php

namespace Netgen\BlockManager\Tests\Exception\Transfer;

use Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException;
use PHPUnit\Framework\TestCase;

final class DataNotAcceptedExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException::noFormatInformation
     */
    public function testNoFormatInformation()
    {
        $exception = DataNotAcceptedException::noFormatInformation();

        $this->assertEquals(
            'Could not find format information in the provided data.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException::typeNotAccepted
     */
    public function testTypeNotAccepted()
    {
        $exception = DataNotAcceptedException::typeNotAccepted('supported', 'unsupported');

        $this->assertEquals(
            'Supported type is "supported", type "unsupported" was given.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException::versionNotAccepted
     */
    public function testVersionNotAccepted()
    {
        $exception = DataNotAcceptedException::versionNotAccepted('supported', 'unsupported');

        $this->assertEquals(
            'Supported version is "supported", version "unsupported" was given.',
            $exception->getMessage()
        );
    }
}
