<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\RoutePrefix;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;

final class RoutePrefixTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\RoutePrefix::handleQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRules(): void
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), 'my_fifth_cool_route');

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
