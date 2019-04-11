<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class TargetListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\TargetList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                TargetList::class,
                str_replace('\TargetList', '', TargetList::class),
                Target::class,
                stdClass::class
            )
        );

        new TargetList([new Target(), new stdClass(), new Target()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\TargetList::__construct
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\TargetList::getTargets
     */
    public function testGetTargets(): void
    {
        $targets = [new Target(), new Target()];

        self::assertSame($targets, (new TargetList($targets))->getTargets());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\TargetList::getTargetIds
     */
    public function testGetTargetIds(): void
    {
        $targets = [Target::fromArray(['id' => 42]), Target::fromArray(['id' => 24])];

        self::assertSame([42, 24], (new TargetList($targets))->getTargetIds());
    }
}
