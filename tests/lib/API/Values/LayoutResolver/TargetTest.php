<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Target::class)]
final class TargetTest extends TestCase
{
    public function testSetProperties(): void
    {
        $targetType = new TargetType1();

        $targetUuid = Uuid::v4();
        $ruleUuid = Uuid::v4();

        $target = Target::fromArray(
            [
                'id' => $targetUuid,
                'ruleId' => $ruleUuid,
                'targetType' => $targetType,
                'value' => 32,
            ],
        );

        self::assertSame($targetUuid->toString(), $target->id->toString());
        self::assertSame($ruleUuid->toString(), $target->ruleId->toString());
        self::assertSame($targetType, $target->targetType);
        self::assertSame(32, $target->value);
    }
}
