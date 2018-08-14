<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\View
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new View(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\View::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\View::getVersion
     */
    public function testGetVersion(): void
    {
        self::assertSame(42, $this->value->getVersion());
    }
}
