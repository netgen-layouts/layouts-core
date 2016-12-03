<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\PathInfo;

class PathInfoTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler\PathInfo::handleQuery
     */
    public function testMatchRules()
    {
        $rules = $this->handler->matchRules($this->getTargetIdentifier(), '/the/answer');

        $this->assertCount(1, $rules);
        $this->assertEquals(7, $rules[0]->id);
    }

    /**
     * Returns the target handler identifier under test.
     *
     * @return string
     */
    protected function getTargetIdentifier()
    {
        return 'path_info';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new PathInfo();
    }
}
