<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\Route;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\Value;

final class RouteTest extends TargetHandlerTestBase
{
    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\Route::handleQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED),
            $this->getTargetIdentifier(),
            'my_cool_route',
        );

        self::assertCount(1, $rules);
        self::assertSame(1, $rules[0]->id);
    }

    /**
     * Returns the target handler identifier under test.
     */
    protected function getTargetIdentifier(): string
    {
        return 'route';
    }

    /**
     * Creates the handler under test.
     */
    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Route();
    }
}
