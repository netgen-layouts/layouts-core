<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Values;

use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ViewTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Values\View
     */
    private $value;

    protected function setUp(): void
    {
        $this->value = new View(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Values\View::__construct
     * @covers \Netgen\Layouts\Serializer\Values\View::getVersion
     */
    public function testGetVersion(): void
    {
        self::assertSame(42, $this->value->getVersion());
    }
}
