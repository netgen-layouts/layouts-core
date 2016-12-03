<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\Route;

class TargetHandlerTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRulesWithNoTargetMatch()
    {
        $rules = $this->handler->matchRules(
            $this->getTargetIdentifier(),
            'some_non_existent_route'
        );

        $this->assertEmpty($rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testMatchRulesWithNonExistentTargetHandler()
    {
        $this->handler->matchRules('non_existent', 'value');
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
     * @return \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Route();
    }
}
