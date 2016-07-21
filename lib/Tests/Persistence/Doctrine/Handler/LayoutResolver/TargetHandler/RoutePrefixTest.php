<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\RoutePrefix;

class RoutePrefixTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\RoutePrefix::handleQuery
     */
    public function testMatchRules()
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), 'my_fifth_cool_route');

        $this->assertCount(1, $rules);
        $this->assertEquals(6, $rules[0]->id);
    }

    /**
     * Returns the target handler identifier under test.
     *
     * @return string
     */
    protected function getTargetIdentifier()
    {
        return 'route_prefix';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new RoutePrefix();
    }
}
