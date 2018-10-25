<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\BlockManager\Exception\Persistence\TargetHandlerException;
use Netgen\BlockManager\Layout\Resolver\TargetHandler\Doctrine\Route;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;

final class TargetHandlerTest extends AbstractTargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRulesWithNoTargetMatch(): void
    {
        $rules = $this->handler->matchRules(
            $this->getTargetIdentifier(),
            'some_non_existent_route'
        );

        self::assertEmpty($rules);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRulesWithNonExistentTargetHandler(): void
    {
        $this->expectException(TargetHandlerException::class);
        $this->expectExceptionMessage('Doctrine target handler for "non_existent" target type does not exist.');

        $this->handler->matchRules('non_existent', 'value');
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
