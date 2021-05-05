<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class TargetTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getRuleId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getTargetType
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Target::getValue
     */
    public function testSetProperties(): void
    {
        $targetType = new TargetType1();

        $targetUuid = Uuid::uuid4();
        $ruleUuid = Uuid::uuid4();

        $target = Target::fromArray(
            [
                'id' => $targetUuid,
                'ruleId' => $ruleUuid,
                'targetType' => $targetType,
                'value' => 32,
            ],
        );

        self::assertSame($targetUuid->toString(), $target->getId()->toString());
        self::assertSame($ruleUuid->toString(), $target->getRuleId()->toString());
        self::assertSame($targetType, $target->getTargetType());
        self::assertSame(32, $target->getValue());
    }
}
