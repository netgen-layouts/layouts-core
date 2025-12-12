<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\Route;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Route::class)]
#[CoversClass(LayoutResolverHandler::class)]
#[CoversClass(LayoutResolverQueryHandler::class)]
final class RouteTest extends TargetHandlerTestBase
{
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published),
            $this->getTargetIdentifier(),
            'my_cool_route',
        );

        self::assertCount(1, $rules);
        self::assertSame(1, $rules[0]->id);
    }

    protected function getTargetIdentifier(): string
    {
        return 'route';
    }

    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Route();
    }
}
