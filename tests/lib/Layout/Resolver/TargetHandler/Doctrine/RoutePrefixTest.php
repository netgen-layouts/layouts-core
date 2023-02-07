<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\RoutePrefix;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\Value;

final class RoutePrefixTest extends TargetHandlerTestBase
{
    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\RoutePrefix::handleQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup('91139748-3bf0-4c25-b45c-d3be6596c399', Value::STATUS_PUBLISHED),
            $this->getTargetIdentifier(),
            'my_fifth_cool_route',
        );

        self::assertCount(1, $rules);
        self::assertSame(6, $rules[0]->id);
    }

    /**
     * Returns the target handler identifier under test.
     */
    protected function getTargetIdentifier(): string
    {
        return 'route_prefix';
    }

    /**
     * Creates the handler under test.
     */
    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new RoutePrefix();
    }
}
