<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Layout\Resolver\TargetHandler\Doctrine\Route;

final class RouteTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetHandler\Doctrine\Route::handleQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRules()
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), 'my_cool_route');

        $this->assertCount(1, $rules);
        $this->assertEquals(1, $rules[0]->id);
    }

    /**
     * Returns the target handler identifier under test.
     *
     * @return string
     */
    protected function getTargetIdentifier()
    {
        return 'route';
    }

    /**
     * Creates the handler under test.
     *
     * \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\TargetHandlerInterface
     */
    protected function getTargetHandler()
    {
        return new Route();
    }
}
