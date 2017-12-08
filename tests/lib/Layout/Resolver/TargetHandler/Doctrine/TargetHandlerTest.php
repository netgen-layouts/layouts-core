<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Layout\Resolver\TargetHandler\Doctrine\Route;

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
     * @expectedException \Netgen\BlockManager\Exception\Persistence\TargetHandlerException
     * @expectedExceptionMessage Doctrine target handler for "non_existent" target type does not exist.
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
     * \Netgen\BlockManager\Layout\Resolver\TargetHandler\Doctrine\TargetHandlerInterface
     */
    protected function getTargetHandler()
    {
        return new Route();
    }
}