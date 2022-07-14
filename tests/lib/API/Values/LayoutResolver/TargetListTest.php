<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class TargetListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\TargetList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Target::class),
                stdClass::class,
            ),
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
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $targets = [Target::fromArray(['id' => $uuid1]), Target::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new TargetList($targets))->getTargetIds());
    }
}
