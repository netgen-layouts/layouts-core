<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\RoutePrefix;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RoutePrefix::class)]
#[CoversClass(LayoutResolverHandler::class)]
#[CoversClass(LayoutResolverQueryHandler::class)]
final class RoutePrefixTest extends TargetHandlerTestBase
{
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup('91139748-3bf0-4c25-b45c-d3be6596c399', Status::Published),
            $this->getTargetIdentifier(),
            'my_fifth_cool_route',
        );

        self::assertCount(1, $rules);
        self::assertSame(6, $rules[0]->id);
    }

    protected function getTargetIdentifier(): string
    {
        return 'route_prefix';
    }

    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new RoutePrefix($this->databaseConnection);
    }
}
