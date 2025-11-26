<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(TargetList::class)]
final class TargetListTest extends TestCase
{
    public function testGetTargets(): void
    {
        $targets = [new Target(), new Target()];

        self::assertSame($targets, TargetList::fromArray($targets)->getTargets());
    }

    public function testGetTargetIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $targets = [Target::fromArray(['id' => $uuid1]), Target::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], TargetList::fromArray($targets)->getTargetIds());
    }
}
